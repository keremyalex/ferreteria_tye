<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <SidebarLayout title="Mi Perfil">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-6 bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Mi Perfil
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Administra la información de tu cuenta y configuraciones de seguridad.
                    </p>
                </div>
            </div>
            
            <div class="space-y-6">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation" class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
                    <div class="p-6">
                        <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    </div>
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword" class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
                    <div class="p-6">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
                    <div class="p-6">
                        <TwoFactorAuthenticationForm
                            :requires-confirmation="confirmsTwoFactorAuthentication"
                        />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
                    <div class="p-6">
                        <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                    </div>
                </div>

                <div v-if="$page.props.jetstream.hasAccountDeletionFeatures" class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
                    <div class="p-6">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
