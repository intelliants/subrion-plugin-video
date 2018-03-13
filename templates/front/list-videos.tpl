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

    <a href="{$smarty.const.IA_URL}video/{$category.slug}/{$entry.id}/" class="v-item__preview" style="background-image: url({$preview});"></a>

    <div class="v-item__content">
        <h4 class="v-item__title">{$entry.title|escape|truncate:50:'...'}</h4>
    </div>
</div>