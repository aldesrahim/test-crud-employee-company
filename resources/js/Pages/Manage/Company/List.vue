<script setup>
import {ref} from "vue"
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AjaxTable from "@/Components/Table/AjaxTable.vue";

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
                    class: "p-0 mr-5"
                },
                {
                    type: "link", // link, default
                    text: "Delete",
                    danger: true,
                    class: "p-0"
                }
            ]
        }
    ],
    rowKey: record => record.id
}

const formProps = {
    state: ref(false),
    company: {},
    getTitle: () => {
        const name = formProps.company?.name;
        return name ? `Edit Company - ${name}` : "Create Company";
    },
    open: (company = null) => {
        formProps.state.value = true;
        formProps.company = company || {};
    },
    reset: () => {
        formProps.company = {};
    }
};

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
                <PrimaryButton class="block" @click="formProps.open()">Create</PrimaryButton>
                <AjaxTable v-bind="tableProps"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
