<script setup>
import {Head, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AjaxTable from "@/Components/Table/AjaxTable.vue";
import CreateModal from "@/Pages/Manage/Company/CreateModal.vue";
import {ref, toRaw} from "vue";
import {notification} from "ant-design-vue";

const tableRef = ref()
const createFormRef = ref()
const deleteConfirm = {
    record: ref(),
    open: ref(false),
    loading: ref(false),
    getTitle: () => {
        const name = deleteConfirm.record.value?.name;

        return `Delete ${name}`
    },
    handleOk: () => {
        const id = deleteConfirm.record.value?.id;
        if (!id) return;

        deleteConfirm.loading.value = true
        router.delete(route('manage.company.destroy', {id}), {
            onError: (params) => {
                notification['error']({
                    message: 'Something went wrong when deleting data',
                    placement: 'topRight',
                })

                console.log(params, toRaw(params))
            },
            onSuccess: () => {
                notification['success']({
                    message: 'Data successfully deleted!',
                    placement: 'topRight',
                })

                reFetchTable()
            },
            onFinish: () => {
                deleteConfirm.loading.value = false
                deleteConfirm.open.value = false
            }
        })
    }
}

const tableProps = {
    apiEndpoint: route('manage.company.index'),
    columns: [
        {
            title: "Index",
            dataIndex: "index",
            rowNumber: true,
        },
        {
            title: "Name",
            dataIndex: "name",
            key: "name",
            filterable: true,
            sortable: true,
        },
        {
            title: "Email",
            dataIndex: "email",
            key: "email",
            filterable: true,
            sortable: true,
        },
        {
            title: "Website",
            dataIndex: "website",
            key: "website",
            filterable: true,
            sortable: true,
            actions: [{
                type: "link",
                target: "_blank",
                class: "p-0",
                href: record => record.website
            }]
        },
        {
            title: "Logo",
            dataIndex: "logo_url",
            key: "logo_url",
            image: {
                src: record => record.logo_url,
                size: "large"
            }
        },
        {
            title: "Action",
            key: "action",
            actions: [
                {
                    type: "link", // link, default
                    text: "Edit",
                    class: "p-0 mr-5",
                    enabled: (record) => !record.has_employees,
                    onClick: (e, record) => {
                        console.log(record)
                    }
                },
                {
                    type: "link", // link, default
                    text: "Delete",
                    danger: true,
                    class: "p-0",
                    enabled: (record) => !record.has_employees,
                    onClick: (e, record) => {
                        if (!record) return;

                        deleteConfirm.record.value = record;
                        deleteConfirm.open.value = true
                    }
                }
            ]
        }
    ],
    rowKey: record => record.id
}

const reFetchTable = () => {
    tableRef.value.reFetch();
}

</script>

<template>
    <Head title="Companies"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Companies
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <PrimaryButton class="block" @click="createFormRef.open = true">Create</PrimaryButton>
                <AjaxTable ref="tableRef" v-bind="tableProps"/>
            </div>
        </div>

        <CreateModal ref="createFormRef" @submitted="() => reFetchTable()"/>

        <a-modal
            v-model:open="deleteConfirm.open.value"
            :title="deleteConfirm.getTitle()"
            :confirm-loading="deleteConfirm.loading.value"
            ok-text="Delete"
            @ok="deleteConfirm.handleOk"
        >
            <p>This action can't be undone and will permanently delete your data</p>
        </a-modal>
    </AuthenticatedLayout>
</template>
