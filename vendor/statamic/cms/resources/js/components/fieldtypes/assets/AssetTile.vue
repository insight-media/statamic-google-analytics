<template>

    <div class="asset-tile"
         :class="{ 'is-image': isImage && !canShowSvg, 'is-svg': canShowSvg, 'is-file': !isImage && !canShowSvg }"
         :title="asset.filename"
    >

        <asset-editor
            v-if="editing"
            :id="asset.id"
            :allow-deleting="false"
            @closed="closeEditor"
            @saved="assetSaved">
        </asset-editor>

        <div class="asset-thumb-container">

            <div class="asset-thumb" :class="{ 'bg-checkerboard': canBeTransparent }">

                <!-- Solo Bard -->
                <template v-if="isImage && isInBardField && !isInAssetBrowser">
                    <img :src="asset.url" >
                </template>

                <template v-else>
                    <img :src="thumbnail" v-if="isImage" :title="label" />

                    <template v-else>
                        <div v-if="canShowSvg"
                             class="svg-img"
                             :style="'background-image:url('+thumbnail+')'">
                        </div>
                        <file-icon v-else :extension="asset.extension" class="p-2 h-40 w-40" />
                    </template>

                </template>

                <div class="asset-controls" v-if="!readOnly">
                    <button
                        @click="edit"
                        class="btn btn-icon icon icon-pencil"
                        :alt="__('Edit')"></button>

                    <button
                        @click="remove"
                        class="btn btn-icon icon icon-trash"
                        :alt="__('Remove')"></button>
                </div>
            </div>
        </div>

        <div class="asset-meta" v-if="showFilename">
            <div class="asset-filename" :title="label">{{ label }}</div>
        </div>
    </div>

</template>


<script>
import Asset from './Asset';

export default {

    mixins: [Asset],

    computed: {

        isInAssetBrowser() {
            let vm = this;

            while (true) {
                let parent = vm.$parent;

                if (! parent) return false;

                if (parent.constructor.name === 'AssetBrowser') {
                    return true;
                }

                vm = parent;
            }
        },

        isInBardField() {
            return this.$parent.isInBardField;
        }
    }
}
</script>
