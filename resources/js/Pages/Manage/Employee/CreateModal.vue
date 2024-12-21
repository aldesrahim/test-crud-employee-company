<script setup>
import {ref, toRaw} from "vue";
import {notification} from "ant-design-vue";
import {useForm} from "@inertiajs/vue3";
import AjaxSelect from "@/Components/Select/AjaxSelect.vue";

const emit = defineEmits(['submitted'])

const loading = ref(false)
const formRef = ref()
const open = ref(false)
const form = useForm({
    company_id: '',
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
})

const onSubmit = () => {
    loading.value = true

    form
        .post(route('manage.employee.store'), {
            onError: (params) => {
                notification['error']({
                    message: 'Something went wrong when saving data',
                    placement: 'topRight',
                })

                console.log(params, toRaw(params))
            },
            onSuccess: () => {
                notification['success']({
                    message: 'Data successfully saved!',
                    placement: 'topRight',
                })

                resetForm()

                emit('submitted')
            },
            onFinish: () => {
                loading.value = false
            }
        })
}

const resetForm = () => {
    formRef.value.resetFields()
    open.value = false
}

defineExpose({
    open,
})

</script>

<template>
    <a-modal
        v-model:open="open"
        title="Create Employee"
        :footer="null"
        @cancel="resetForm"
    >
        <a-form
            ref="formRef"
            layout="vertical"
            :model="form"
            @submit.prevent="onSubmit"
        >
            <a-form-item
                name="company_id"
                label="Company"
                :validate-status="form.errors.company_id ? 'error' : ''"
                :help="form.errors.company_id ?? ''"
            >
                <AjaxSelect :api-endpoint="route('manage.company.index')" v-model="form.company_id" />
            </a-form-item>

            <a-form-item
                name="first_name"
                label="Fist Name"
                :validate-status="form.errors.first_name ? 'error' : ''"
                :help="form.errors.first_name ?? ''"
            >
                <a-input v-model:value="form.first_name" class="rounded-md shadow-sm"/>
            </a-form-item>

            <a-form-item
                name="last_name"
                label="Last Name"
                :validate-status="form.errors.last_name ? 'error' : ''"
                :help="form.errors.last_name ?? ''"
            >
                <a-input v-model:value="form.last_name" class="rounded-md shadow-sm"/>
            </a-form-item>

            <a-form-item
                name="email"
                label="Email"
                :validate-status="form.errors.email ? 'error' : ''"
                :help="form.errors.email ?? ''"
            >
                <a-input v-model:value="form.email" class="rounded-md shadow-sm"/>
            </a-form-item>

            <a-form-item
                name="phone"
                label="Phone"
                :validate-status="form.errors.phone ? 'error' : ''"
                :help="form.errors.phone ?? ''"
            >
                <a-input v-model:value="form.phone" class="rounded-md shadow-sm" placeholder="International phone number (eg. +62xx)" />
            </a-form-item>


            <div class="flex gap-x-2">
                <a-button type="primary" html-type="submit" :loading="loading">
                    Submit
                </a-button>
            </div>
        </a-form>
    </a-modal>
</template>
