<script setup>
const emit = defineEmits(["search", "reset"])
const props = defineProps({
    column: Object,
    setSelectedKeys: Function,
    selectedKeys: Array,
    confirm: Function,
    clearFilters: Function
})

const handleSearch = () => {
    props.confirm()
    emit("search", props.selectedKeys[0])
}

const handleReset = () => {
    props.clearFilters({confirm: true})
    emit("reset")
}

</script>

<template>
    <div class="p-2">
        <a-input
            :placeholder="`Search ${column.title}`"
            :value="selectedKeys[0]"
            @change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])"
            @pressEnter="handleSearch"
            size="small"
            class="block rounded-md shadow-sm text-sm"
        />
        <div class="flex gap-2 mt-2">
            <a-button type="primary" size="small" block @click="handleSearch">Search</a-button>
            <a-button size="small" block @click="handleReset">Reset</a-button>
        </div>
    </div>
</template>
