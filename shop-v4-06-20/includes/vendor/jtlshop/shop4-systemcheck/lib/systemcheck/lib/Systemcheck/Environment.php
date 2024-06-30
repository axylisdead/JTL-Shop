<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Environment
 */
class Systemcheck_Environment
{
    /**
     * passed
     * @var bool
     */
    protected $passed;

    /**
     * getIsPassed
     * @return bool
     */
    public function getIsPassed()
    {
        return $this->passed;
    }

    /**
     * Enumerate tests for a specific test group name
     *
     * @param string $group
     * @return array
     */
    private function getTests($group)
    {
        $files  = array();
        $folder = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . $group;

        if (is_dir($folder)) {
            if (($dh = opendir($folder)) !== false) {
                while (($file = readdir($dh)) !== false) {
                    // skip hidden files too! (starting with dots in 'nix-like systems),
                    // and skip "_"-starting files, to make "deactivation" as simple as possible in the filesystem
                    if ($file === '.' || $file === '..' || 0 === strpos($file, '.') || 0 === strpos($file, '_')) {
                        continue;
                    }
                    if (is_dir($folder . '/' . $file)) {
                        continue;
                    }
                    $files[] = $file;
                }
                closedir($dh);
            }
        }

        foreach ($files as $key => $file) {
            $files[$key] = 'Systemcheck_Tests_' . $group . '_' . rtrim($file, '.php');
        }

        return $files;
    }


    /**
     * Execute test group
     *
     * @param string $group
     * @return array
     */
    public function executeTestGroup($group)
    {
        $result = array(
            'recommendations' => array(),
            'apache_config'   => array(),
            'php_config'      => array(),
            'php_modules'     => array(),
            'programs'        => array()
        );
        $tests  = $this->getTests($group);

        $this->passed    = true;
        $vCompletedTests = array();
        foreach ($tests as $test) {
            /** @var Systemcheck_Tests_Test $testObject */
            $testObject = new $test();
            // check a property here, if that test is "replacable by one other".
            // if that is the case, we skip this test (and "continue;" to the next one).
            if (($szReplacement = $testObject->getIsReplaceableBy()) !== false) {
                // prevents double execution of one Test
                if (in_array($szReplacement, array_keys($vCompletedTests))) {
                    $bReplacementResult = $vCompletedTests[$szReplacement]->getResult();
                } else {
                    /** @var Systemcheck_Tests_Test $oReplacementTest */
                    $oReplacementTest = new $szReplacement();
                    $oReplacementTest->execute();
                    $bReplacementResult = $oReplacementTest->getResult();
                }
                // a Test can replaced by Another, if the Other is "optional" (and/or "recommend")
                if ($bReplacementResult === Systemcheck_Tests_Test::RESULT_OK) {
                    continue; // skip the "execution" and "listing" of this current test
                }
            }
            $vCompletedTests[get_class($testObject)] = $testObject; // store "completed" to prevent double-testing above

            $testObject->execute();

            if ($testObject->getResult() !== Systemcheck_Tests_Test::RESULT_OK) {
                if (!$testObject->getIsOptional()) {
                    $this->passed = false;
                } elseif ($testObject->getIsRecommended()) {
                    $result['recommendations'][] = $testObject;
                }
            }

            if ($testObject instanceof Systemcheck_Tests_ApacheConfigTest) {
                $result['apache_config'][] = $testObject;
            } elseif ($testObject instanceof Systemcheck_Tests_PhpConfigTest) {
                $result['php_config'][] = $testObject;
            } elseif ($testObject instanceof Systemcheck_Tests_PhpModuleTest) {
                $result['php_modules'][] = $testObject;
            } elseif ($testObject instanceof Systemcheck_Tests_ProgramTest) {
                $result['programs'][] = $testObject;
            }
        }

        return $result;
    }
}
