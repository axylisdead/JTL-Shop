<?php

/**
 * Interface ICallbackNamed
 */
interface ICallbackNamed
{
    /**
     * @return mixed
     */
    public function hasName();

    /**
     * @return mixed
     */
    public function getName();
}

/**
 * Callback class introduces currying-like pattern.
 *
 * Example:
 * function foo($param1, $param2, $param3) {
 *   var_dump($param1, $param2, $param3);
 * }
 * $fooCurried = new Callback('foo',
 *   'param1 is now statically set',
 *   new CallbackParam, new CallbackParam
 * );
 * phpQuery::callbackRun($fooCurried,
 *    array('param2 value', 'param3 value'
 * );
 *
 * Callback class is supported in all phpQuery methods which accepts callbacks.
 *
 * @link http://code.google.com/p/phpquery/wiki/Callbacks#Param_Structures
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 *
 * @TODO??? return fake forwarding function created via create_function
 * @TODO honor paramStructure
 */
class Callback implements ICallbackNamed
{
    /**
     * @var null
     */
    public $callback = null;

    /**
     * @var array|null
     */
    public $params = null;

    /**
     * @var
     */
    public $name;

    /**
     * Callback constructor.
     * @param      $callback
     * @param null $param1
     * @param null $param2
     * @param null $param3
     */
    public function __construct($callback, $param1 = null, $param2 = null, $param3 = null)
    {
        $params = func_get_args();
        $params = array_slice($params, 1);
        if ($callback instanceof Callback) {
            // TODO implement recurention
        } else {
            $this->callback = $callback;
            $this->params   = $params;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Callback: ' . $this->name;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return isset($this->name) && $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    // TODO test me
//	public function addParams() {
//		$params = func_get_args();
//		return new Callback($this->callback, $this->params+$params);
//	}
}

/**
 * Shorthand for new Callback(create_function(...), ...);
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 */
class CallbackBody extends Callback
{
    /**
     * CallbackBody constructor.
     * @param      $paramList
     * @param null $code
     * @param null $param1
     * @param null $param2
     * @param null $param3
     */
    public function __construct($paramList, $code, $param1 = null, $param2 = null, $param3 = null)
    {
        $params         = func_get_args();
        $params         = array_slice($params, 2);
        $this->callback = create_function($paramList, $code);
        $this->params   = $params;
    }
}

/**
 * Callback type which on execution returns reference passed during creation.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 */
class CallbackReturnReference extends Callback implements ICallbackNamed
{
    /**
     * @var
     */
    protected $reference;

    /**
     * CallbackReturnReference constructor.
     * @param      $reference
     * @param null $name
     */
    public function __construct(&$reference, $name = null)
    {
        $this->reference =& $reference;
        $this->callback  = array($this, 'callback');
    }

    /**
     * @return mixed
     */
    public function callback()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Callback: ' . $this->name;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return isset($this->name) && $this->name;
    }
}

/**
 * Callback type which on execution returns value passed during creation.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 */
class CallbackReturnValue extends Callback implements ICallbackNamed
{
    /**
     * @var
     */
    protected $value;
    public $name;

    /**
     * CallbackReturnValue constructor.
     * @param      $value
     * @param null $name
     */
    public function __construct($value, $name = null)
    {
        $this->value    =& $value;
        $this->name     = $name;
        $this->callback = array($this, 'callback');
    }

    /**
     * @return mixed
     */
    public function callback()
    {
        return $this->value;
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Callback: ' . $this->name;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return isset($this->name) && $this->name;
    }
}

/**
 * CallbackParameterToReference can be used when we don't really want a callback,
 * only parameter passed to it. CallbackParameterToReference takes first
 * parameter's value and passes it to reference.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 */
class CallbackParameterToReference extends Callback
{
    /**
     * @param $reference
     * @TODO implement $paramIndex;
     * param index choose which callback param will be passed to reference
     */
    public function __construct(&$reference)
    {
        $this->callback =& $reference;
    }
}

/**
 * Class CallbackParam
 */
class CallbackParam
{
}
