<script setup>
import {useRequest} from "vue-request";
import {computed} from "vue";
import {debounce} from "@/helpers.js";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null
    },
    apiEndpoint: {
        type: String,
        required: true
    },
    filters: {
        type: [Array, String],
        default: "name"
    },
    placeholder: {
        type: String,
        default: "Search..."
    },
    perPage: {
        type: Number,
        default: 20
    },
    keyLabel: {
        type: String,
        default: "name"
    },
    keyValue: {
        type: String,
        default: "id"
    },
    debounce: {
        type: Number,
        default: 500
    }
});

const service = (params) => {
    return window.axios(props.apiEndpoint, {responseType: "json", params});
};
const {data, loading, run} = useRequest(service, {
    defaultParams: [{page: {number: 1, size: props.perPage}}]
});

const options = computed(() => {
    return data.value?.data.data.map(item => ({
        label: item[props.keyLabel],
        value: item[props.keyValue]
    }));
});

const handleSearch = debounce((query) => {
    if (loading.value) return "";

    const filter = {};
    const filterKey = props.filters;

    if (filterKey instanceof Array) {
        filterKey?.forEach(key => {
            filter[key] = query;
        });
    } else {
        filter[filterKey] = query;
    }

    run({
        page: {number: 1, size: props.perPage},
        filter
    });
}, props.debounce);

const proxyValue = computed({
    get() {
        return props.modelValue;
    },
    set(val) {
        emit("update:modelValue", val);
    }
});
</script>

<template>
    <a-select
        v-model:value="proxyValue"
        show-search
        :placeholder="placeholder"
        :loading="loading"
        :default-active-first-option="false"
        :show-arrow="false"
        :filter-option="false"
        :not-found-content="null"
        :options="options"
        @search="handleSearch"
        class="block mb-2 rounded-md shadow-sm ant-custom-ajax-filter-select text-sm"
    >
        <template v-if="loading" #notFoundContent>
            <a-spin size="small"/>
        </template>
    </a-select>
</template>
