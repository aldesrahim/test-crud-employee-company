<script setup>
import {ref, toRaw} from "vue";
import {PlusOutlined} from '@ant-design/icons-vue'
import {notification} from "ant-design-vue";
import {useForm} from "@inertiajs/vue3";

const emit = defineEmits(['submitted'])

const loading = ref(false)
const formRef = ref()
const open = ref(false)
const form = useForm({
    name: '',
    email: '',
    website: '',
    logo: [],
})

const onSubmit = () => {
    loading.value = true

    form
        .transform((data) => {
            return {
                ...data,
                attachment_logo: data.logo[0]?.originFileObj ?? null,
            }
        })
        .post(route('manage.company.store'), {
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

const beforeUpload = file => {
    form.logo.value = [...(form.logo.value || []), file];
    return false;
};

defineExpose({
    open,
})

</script>

<template>
    <a-modal
        v-model:open="open"
        title="Create Company"
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
                name="name"
                label="Name"
                :validate-status="form.errors.name ? 'error' : ''"
                :help="form.errors.name ?? ''"
            >
                <a-input v-model:value="form.name" class="rounded-md shadow-sm"/>
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
                name="website"
                label="Website"
                :validate-status="form.errors.website ? 'error' : ''"
                :help="form.errors.website ?? ''"
            >
                <a-input v-model:value="form.website" class="rounded-md shadow-sm"/>
            </a-form-item>

            <a-form-item label="Logo" name="logo">
                <a-upload
                    v-model:file-list="form.logo"
                    name="logo"
                    list-type="picture-card"
                    :max-count="1"
                    :before-upload="beforeUpload"
                >
                    <div class="flex flex-col items-center">
                        <plus-outlined></plus-outlined>
                        <span>Upload</span>
                    </div>
                </a-upload>
            </a-form-item>

            <div class="flex gap-x-2">
                <a-button type="primary" html-type="submit" :loading="loading">
                    Submit
                </a-button>
            </div>
        </a-form>
    </a-modal>
</template>
