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

    {ia_add_media files='css: _IA_URL_modules/video/templates/front/css/style'}
{else}
    <div class="alert alert-info">{lang key='no_videos'}</div>
{/if}