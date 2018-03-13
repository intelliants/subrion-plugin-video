<h3 class="text-center">{$category.title}</h3><hr/>
{if !empty($entries)}
    <div class="v-items">
        <div class="row">
            {foreach $entries as $entry}
                <div class="col-md-4">
                    {include file='module:video/list-videos.tpl'}
                </div>
                {if $entry@iteration % 3 == 0 && !$entry@last}
                    </div>
                    <div class="row">
                {/if}
            {/foreach}
        </div>
    </div>

    {navigation aTotal=$pagination.total aTemplate=$pagination.url aItemsPerPage=$pagination.limit aNumPageItems=5}

{else}
    <div class="alert alert-info">{lang key='no_videos'}</div>
{/if}