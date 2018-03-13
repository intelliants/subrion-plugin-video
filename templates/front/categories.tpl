{if !empty($categories)}
    <div class="v-items">
        <div class="row">
            {foreach $categories as $cat}
                <div class="col-md-4"><a href="{$smarty.const.IA_URL}video/{$cat.slug}/">{$cat.title|escape}</a></div>
            {/foreach}
        </div>
    </div>
{/if}