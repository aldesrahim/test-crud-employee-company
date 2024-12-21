<script setup>
import {ref, toRaw} from "vue";
import {PlusOutlined} from '@ant-design/icons-vue'
import {notification} from "ant-design-vue";
import {useForm} from "@inertiajs/vue3";

const emit = defineEmits(['submitted'])

const formRef = ref()
const loading = ref(false)
const open = ref(false)
const form = useForm({
    name: '',
    email: '',
    website: '',
    logo: [],
})

const companyData = ref();
const logoPreview = ref(false)
const setVisibleLogoPreview = value => {
    logoPreview.value = value
}

const openModal = async (companyId) => {
    const url = route('manage.company.show', {company: companyId});
    window.axios.get(url)
        .then(response => {
            companyData.value = response.data?.data
            fillForm()
        })
        .catch(error => {
            notification['error']({
                message: `Unable to retrieve company ${companyData.value?.name} data`,
                placement: 'topRight',
            })

            console.log('Error', error.message);
        })
}

const fillForm = () => {
    if (!companyData.value?.id) {
        notification['error']({
            message: `Company with ID: ${companyData.value?.id} could not be found`,
            placement: 'topRight',
        })

        return
    }

    if (companyData.value?.has_employees) {
        notification['error']({
            message: `Unable to update company ${companyData.value?.name}, because it constrained to employees`,
            placement: 'topRight',
        })
    }

    open.value = true
    form.name = companyData.value.name
    form.email = companyData.value.email
    form.website = companyData.value.website
}

const onSubmit = () => {
    loading.value = true

    form
        .transform((data) => {
            return {
                ...data,
                _method: 'PUT',
                attachment_logo: data.logo[0]?.originFileObj ?? null,
            }
        })
        .post(route('manage.company.update', {id: companyData.value?.id}), {
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
    openModal
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
                        <span v-if="companyData?.logo_url">Change Logo</span>
                        <span v-else>Upload</span>
                    </div>
                </a-upload>

                <a-anchor-link
                    type="link"
                    v-show="companyData?.logo_url"
                    class="p-0 my-0 text-sm cursor-pointer text-blue-500"
                    @click="() => setVisibleLogoPreview(true)"
                >
                    (Preview Current Logo)
                </a-anchor-link>
            </a-form-item>

            <a-image
                v-show="companyData?.logo_url"
                :width="200"
                :style="{ display: 'none' }"
                :preview="{
                        visible: logoPreview,
                        onVisibleChange: setVisibleLogoPreview
                    }"
                :src="companyData?.logo_url"
            />

            <div class="flex gap-x-2">
                <a-button type="primary" html-type="submit" :loading="loading">
                    Submit
                </a-button>
            </div>
        </a-form>
    </a-modal>
</template>
