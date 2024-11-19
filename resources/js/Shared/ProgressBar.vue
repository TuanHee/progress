<template>
    <div class="flex justify-end">
        <span class="text-xs">{{ percent }}</span>
    </div>
    <div class="flex h-2 bg-gray-100 rounded-md overflow-hidden">
        <div class="bg-primary whitespace-nowrap text-sm" :style="'width: '+ percent +';'"></div>
    </div>
</template>

<script>
    export default {
        props: {
            percent: Number,
        },
    }
</script>
