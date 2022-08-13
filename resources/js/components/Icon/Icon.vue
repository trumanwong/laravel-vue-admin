<template>
    <i :class="classes" aria-hidden="true"></i>
</template>

<script>
import {computed, defineComponent} from 'vue'

export default defineComponent({
    props: {
        className: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        return {
            classes: computed(() => {
                if (props.className) {
                    return `custom-icon bi bi-${props.className}`
                }
                return 'custom-icon'
            })
        }
    }
})
</script>

<style scope lang="scss">
.sub-el-icon,
.nav-icon {
    display: inline-block;
    font-size: 18px;
    margin-right: 12px;
    position: relative;
}

.custom-icon {
    width: 1em;
    height: 1em;
    font-size: 1.5rem;
    position: relative;
    fill: currentColor;
    vertical-align: -2px;
}
</style>
