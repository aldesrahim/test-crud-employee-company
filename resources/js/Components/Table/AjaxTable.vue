<script setup>
import {computed} from "vue";
import {useRequest} from "vue-request";
import Filter from "@/Components/Table/Filter.vue";
import {removeProp} from "@/helpers.js";

const props = defineProps({
    apiEndpoint: {
        type: String,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    rowKey: {
        type: [String, Function],
        default: "id"
    }
})

const processedColumns = computed(() => props.columns.map(col => ({
    ...col,
    sorter: col.sortable || false,
    customFilterDropdown: col.filterable || false
})));

const attributes = computed(() => {
    const props = {
        hasCustomFilter: null,
        rowNumberColumns: [],
        actionColumns: [],
        imageColumns: [],
    }

    processedColumns.value.forEach(col => {
        if (col.customFilterDropdown !== false) {
            props.hasCustomFilter = true
        }

        if (col.rowNumber) {
            props.rowNumberColumns.push(col)
        }

        if (col.actions) {
            props.actionColumns.push(col)
        }

        if (col.image) {
            props.imageColumns.push(col)
        }
    })

    return props
})

const {data, loading, run} = useRequest(params => window.axios(props.apiEndpoint, {params, responseType: 'json'}), {
    defaultParams: [{
        page: {
            number: 1,
            size: 10,
        }
    }]
})

const response = computed(() => data.value?.data)
const tableData = computed(() => response.value?.data || [])
const tablePagination = computed(() => ({
    current: response.value?.current_page,
    pageSize: response.value?.per_page,
    total: response.value?.total,
    offset: response.value?.per_page * (response.value?.current_page - 1)
}))

let tableFilters = {}
let tableSorter = null

const getSortString = () => {
    if (!tableSorter.field) return "";

    const prefix = tableSorter.order === "ascend" ? "" : "-";
    return `${prefix}${tableSorter.field}`;
};

const getFilterParams = () => {
    const params = {};

    Object.keys(tableFilters).forEach((key) => {
        if (tableFilters[key]) {
            params[`filter[${key}]`] = tableFilters[key];
        }
    });

    return params;
};

const reFetch = (number = 1, size = null) => {
    size = size ?? tablePagination.value.pageSize;

    run({
        page: {number, size},
        sort: getSortString(),
        ...getFilterParams()
    });
};

const handleTableChange = (pagination, filters, sorter) => {
    tableSorter = sorter;

    reFetch(pagination.current, pagination.pageSize);
};

const handleSearchFilter = (value, confirm, field) => {
    confirm();
    tableFilters = {...tableFilters, [field]: value};
    reFetch();
};

const resetSearchFilter = (clearFilters) => {
    clearFilters({confirm: true});
    tableFilters = {};
    reFetch();
};

</script>

<template>
    <a-table
        :columns="processedColumns"
        :data-source="tableData"
        :loading="loading"
        :pagination="tablePagination"
        @change="handleTableChange"
        :row-key="rowKey"
    >
        <template #customFilterDropdown="filterProps" v-if="attributes.hasCustomFilter">
            <Filter
                v-bind="filterProps"
                @search="handleSearchFilter"
                @reset="resetSearchFilter"
            />
        </template>

        <template #bodyCell="{text, record, index, column}">
            <template v-for="col in attributes.rowNumberColumns">
                <template v-if="col.key === column.key">
                    {{ index + 1 + tablePagination.offset }}
                </template>
            </template>

            <template v-for="col in attributes.imageColumns">
                <template v-if="col.key === column.key">
                    <a-avatar
                        v-bind="removeProp(col.image, 'src')"
                        :src="typeof col.image.src === 'function' ? col.image.src(record) : col.image.src"
                    />
                </template>
            </template>

            <template v-for="col in attributes.actionColumns">
                <template v-if="col.key === column.key">
                    <template v-for="action in col.actions">
                        <a-button
                            v-bind="removeProp(action, 'href')"
                            :href="typeof action.href === 'function' ? action.href(record) : action.href"
                            @click="action.onClick($event, record)"
                        >
                            {{ action.text ?? text }}
                        </a-button>
                    </template>
                </template>
            </template>
        </template>
    </a-table>
</template>
