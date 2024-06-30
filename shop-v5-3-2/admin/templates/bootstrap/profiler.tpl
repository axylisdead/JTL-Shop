{include file='tpl_inc/header.tpl'}
{include file='tpl_inc/seite_header.tpl' cTitel=__('Profiler') cBeschreibung=__('profilerDesc') cDokuURL=__('pluginprofilerURL')}
<div id="content">
    <div class="card">
        <div class="card-body">
            <form class="delete-run" action="{$adminURL}{$route}" method="post">
                {$jtl_token}
                <input type="hidden" value="y" name="delete-all" />
                <button type="submit" class="btn btn-danger" name="delete-run-submit">
                    <i class="fas fa-trash-alt"></i> {__('deleteAll')}
                </button>
            </form>
        </div>
    </div>
    <div class="content">
        {if $sqlProfilerData !== null && count($sqlProfilerData) > 0}
            <div class="accordion" id="accordion2" role="tablist" aria-multiselectable="true">
                {foreach $sqlProfilerData as $run}
                    <div class="card">
                        <div class="card-header" role="tab" data-idx="{$run@index}" id="heading-sql-profile-{$run@index}">
                            <div class="subheading1">
                                <a data-toggle="collapse" data-parent="#accordion2" href="#sql-profile-{$run@index}" aria-expanded="true" aria-controls="profile-{$run@index}">
                                    <span class="badge badge-primary">{$run->runID}</span> {$run->url} - {$run->timestamp} - {$run->total_time}s
                                </a>
                            </div>
                        </div>
                        <div id="sql-profile-{$run@index}" class="collapse collapse" role="tabpanel" aria-labelledby="heading-sql-profile-{$run@index}">
                            <div class="card-body">
                                <p><span class="label2">{__('Total queries')}: </span> <span class="text"> {$run->total_count}</span></p>
                                <p><span class="label2">{__('Runtime')}: </span> <span class="text"> {$run->total_time}</span></p>
                                <p><span class="label2">{__('Tables')}:</span></p>
                                <ul class="affacted-tables">
                                    {foreach $run->data as $query}
                                        <li class="list a-table">
                                            <strong>{$query->tablename}</strong> ({$query->runcount} times, {$query->runtime}s)<br />
                                            {if $query->statement !== null}
                                                <strong>{__('Statement')}:</strong> <code class="sql">{$query->statement}</code><br />
                                            {/if}
                                            {if $query->data !== null}
                                                {assign var=data value=unserialize($query->data)}
                                                <strong>{__('Backtrace')}:</strong>
                                                <ol class="backtrace">
                                                    {foreach $data.backtrace as $backtrace}
                                                        <li class="list bt-item">{$backtrace.file}:{$backtrace.line} - {if $backtrace.class !== ''}{$backtrace.class}::{/if}{$backtrace.function}()</li>
                                                    {/foreach}
                                                </ol>
                                                {if isset($data.message)}
                                                    <strong>{__('Error message')}:</strong>
                                                    {$data.message}
                                                {/if}
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            <div class="card-footer save-wrapper">
                                <form class="delete-run" action="{$adminURL}{$route}" method="post">
                                    {$jtl_token}
                                    <input type="hidden" value="sqlprofiler" name="tab" />
                                    <input type="hidden" value="{$run->runID}" name="run-id" />
                                    <div class="row">
                                        <div class="ml-auto col-sm-6 col-xl-auto">
                                            <button type="submit" class="btn btn-danger btn-block" name="delete-run-submit">
                                                {__('deleteEntry')}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        {else}
            <div class="alert alert-info"><i class="fal fa-info-circle"></i> {__('noDataAvailable')}</div>
        {/if}
    </div>
</div>
{include file='tpl_inc/footer.tpl'}
