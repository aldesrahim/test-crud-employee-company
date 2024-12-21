<script setup>
import {Head, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AjaxTable from "@/Components/Table/AjaxTable.vue";
import CreateModal from "@/Pages/Manage/Employee/CreateModal.vue";
import {ref, toRaw} from "vue";
import {notification} from "ant-design-vue";
import UpdateModal from "@/Pages/Manage/Employee/UpdateModal.vue";
import CompanyInfo from "@/Pages/Manage/Employee/CompanyInfo.vue";

const tableRef = ref()
const createFormRef = ref()
const updateFormRef = ref()
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
        router.delete(route('manage.employee.destroy', {id}), {
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
    apiEndpoint: route('manage.employee.index'),
    columns: [
        {
            title: "Index",
            dataIndex: "index",
            rowNumber: true,
        },
        {
            title: "Full Name",
            dataIndex: "full_name",
            key: "full_name",
            filterable: true,
            sortable: true,
        },
        {
            title: "Company",
            dataIndex: "company_name",
            key: "company_name",
            filterable: true,
            sortable: true,
            actions: [{
                type: "link",
                class: "p-0",
                onClick: (e, record) => companyProps.open(record?.company || {})
            }]
        },
        {
            title: "Email",
            dataIndex: "email",
            key: "email",
            filterable: true,
            sortable: true,
        },
        {
            title: "Phone",
            dataIndex: "phone",
            key: "phone",
            filterable: true,
            sortable: true,
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
                        if (!record) return;

                        updateFormRef.value.openModal(record.id)
                    }
                },
                {
                    type: "link", // link, default
                    text: "Delete",
                    danger: true,
                    class: "p-0",
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

const companyProps = {
    state: ref(false),
    company: {},
    open: (company) => {
        companyProps.state.value = true;
        companyProps.company = company;
    },
    reset: () => {
        companyProps.company = {};
    }
};

const reFetchTable = () => {
    tableRef.value.reFetch();
}

</script>

<template>
    <Head title="Employees"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Employees
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <PrimaryButton class="block" @click="createFormRef.open = true">Create</PrimaryButton>
                <AjaxTable ref="tableRef" v-bind="tableProps"/>
            </div>
        </div>

        <CreateModal ref="createFormRef" @submitted="() => reFetchTable()"/>
        <UpdateModal ref="updateFormRef" @submitted="() => reFetchTable()"/>

        <a-modal
            v-model:open="deleteConfirm.open.value"
            :title="deleteConfirm.getTitle()"
            :confirm-loading="deleteConfirm.loading.value"
            ok-text="Delete"
            @ok="deleteConfirm.handleOk"
        >
            <p>This action can't be undone and will permanently delete your data</p>
        </a-modal>

        <a-modal
            v-model:open="companyProps.state.value"
            title="Company Information"
            :footer="null"
            :after-close="companyProps.reset"
        >
            <CompanyInfo :company="companyProps.company" />
        </a-modal>
    </AuthenticatedLayout>
</template>
