{if !empty($video_latest)}
    <div class="v-items">
        {foreach $video_latest as $entry}
            <div class="v-item">
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

                <a href="{$smarty.const.IA_URL}video/{$entry.slug}/{$entry.id}/" class="v-item__preview" style="background-image: url({$preview});"></a>

                <div class="v-item__content">
                    <h4 class="v-item__title">{$entry.title|escape|truncate:50:'...'}</h4>
                </div>
            </div>
        {/foreach}
    </div>

    {ia_add_media files='css:_IA_URL_modules/video/templates/front/css/style'}
{else}
    <div class="alert alert-info">{lang key='no_videos'}</div>
{/if}