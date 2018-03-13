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

    {ia_add_media files='css:_IA_URL_modules/video/templates/front/css/style'}
{elseif !empty($entry)}
    <div class="v-item-view">
        {if $core.config.video_display_preview}
            {if !empty($entry.preview)}
                {$preview = {ia_image file=$entry.preview type='thumbnail' url=true}}
            {elseif (!empty($entry.youtube_preview))}
                {$preview = $entry.youtube_preview}
            {elseif (!empty($entry.vimeo_preview))}
                {$preview = $entry.vimeo_preview}
            {else}
                {$preview = "{$smarty.const.IA_CLEAR_URL}modules/video/templates/front/img/preview.png"}
            {/if}
        {else}
            {$preview = "{$smarty.const.IA_CLEAR_URL}modules/video/templates/front/img/preview.png"}
        {/if}

        {switch $entry.source}
            {case 'youtube' break}
                <iframe src="https://www.youtube.com/embed/{$entry.video_id}?showinfo=0&autoplay={$core.config.video_autoplay}" width="{$core.config.video_width}" height="{$core.config.video_height}" frameborder="0" allowfullscreen></iframe>
            {case 'vimeo' break}
                <iframe src="http://player.vimeo.com/video/{$entry.video_id}?api=1;title=0&autoplay={$core.config.video_autoplay}" width="{$core.config.video_width}" height="{$core.config.video_height}" frameborder="0" allowfullscreen></iframe>
            {case 'custom_upload' break}
                <video width="{$core.config.video_width}" height="{$core.config.video_height}" poster="{$preview}" controls="controls"{if $core.config.video_autoplay} autoplay{/if}>
                    {if !empty($entry.file_mp4)}
                        {$mp4 = array_shift($entry.file_mp4)}
                        <source src="{$smarty.const.IA_CLEAR_URL}uploads/{$mp4['path']}{$mp4['file']}" type="video/mp4">
                    {/if}
                    {if !empty($entry.file_webm)}
                        {$webm = array_shift($entry.file_webm)}
                        <source src="{$smarty.const.IA_CLEAR_URL}uploads/{$webm['path']}{$webm['file']}" type="video/webm">
                    {/if}
                    {if !empty($entry.file_ogg)}
                        {$ogg = array_shift($entry.file_ogg)}
                        <source src="{$smarty.const.IA_CLEAR_URL}uploads/{$ogg['path']}{$ogg['file']}" type="video/ogg">
                    {/if}
                </video>
        {/switch}

        <div class="v-item-view__info">
            <div class="pull-left">
                <span class="v-item-view__info-item">
                    <span class="fa fa-calendar"></span>
                    {lang key='posted_on'} {$entry.date_added|date_format:$core.config.date_format}</span>
                </span>
                <span class="v-item-view__info-item">
                    <span class="fa fa-eye"></span> {$entry.views_num} {lang key='views'}
                </span>
            </div>
            <div class="pull-right">
                <ul class="list-inline share-buttons">
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={$smarty.const.IA_SELF|escape:'url'}&t={$entry.title}" target="_blank" title="Share on Facebook"><i class="fa fa-facebook-square fa-2x"></i></a></li>
                    <li><a href="https://twitter.com/intent/tweet?source={$smarty.const.IA_SELF|escape:'url'}&text={$entry.title}:{$smarty.const.IA_SELF|escape:'url'}" target="_blank" title="Tweet"><i class="fa fa-twitter-square fa-2x"></i></a></li>
                    <li><a href="https://plus.google.com/share?url={$smarty.const.IA_SELF|escape:'url'}" target="_blank" title="Share on Google+"><i class="fa fa-google-plus-square fa-2x"></i></a></li>
                    <li><a href="http://pinterest.com/pin/create/button/?url={$smarty.const.IA_SELF|escape:'url'}&media={$preview}" target="_blank" title="Pin it"><i class="fa fa-pinterest-square fa-2x"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

    {ia_add_media files='css:_IA_URL_modules/video/templates/front/css/style'}
{else}
    <div class="alert alert-info">{lang key='no_videos'}</div>
{/if}