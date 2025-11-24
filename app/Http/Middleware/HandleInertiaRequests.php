<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'locale' => app()->getLocale(),
            'translations' => fn () => [
                'Profile Information' => __('Profile Information'),
                'Update Password' => __('Update Password'),
                'Two Factor Authentication' => __('Two Factor Authentication'),
                'Browser Sessions' => __('Browser Sessions'),
                'Delete Account' => __('Delete Account'),
                'You have not enabled two factor authentication.' => __('You have not enabled two factor authentication.'),
                'You have enabled two factor authentication.' => __('You have enabled two factor authentication.'),
                'Finish enabling two factor authentication.' => __('Finish enabling two factor authentication.'),
                'Update your account\'s profile information and email address.' => __('Update your account\'s profile information and email address.'),
                'Ensure your account is using a long, random password to stay secure.' => __('Ensure your account is using a long, random password to stay secure.'),
                'Add additional security to your account using two factor authentication.' => __('Add additional security to your account using two factor authentication.'),
                'Manage and log out your active sessions on other browsers and devices.' => __('Manage and log out your active sessions on other browsers and devices.'),
                'Permanently delete your account.' => __('Permanently delete your account.'),
                'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.' => __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.'),
                'To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.' => __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.'),
                'Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.' => __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.'),
                'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.' => __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.'),
                'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.' => __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.'),
                'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.' => __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'),
                'Save' => __('Save'),
                'Saved.' => __('Saved.'),
                'Current Password' => __('Current Password'),
                'New Password' => __('New Password'),
                'Confirm Password' => __('Confirm Password'),
                'Name' => __('Name'),
                'Email' => __('Email'),
                'Photo' => __('Photo'),
                'Select A New Photo' => __('Select A New Photo'),
                'Remove Photo' => __('Remove Photo'),
                'Code' => __('Code'),
                'Setup Key' => __('Setup Key'),
                'Enable' => __('Enable'),
                'Confirm' => __('Confirm'),
                'Regenerate Recovery Codes' => __('Regenerate Recovery Codes'),
                'Show Recovery Codes' => __('Show Recovery Codes'),
                'Disable' => __('Disable'),
                'Done' => __('Done'),
                'profile.2fa_title' => __('Autenticación de dos factores'),
                'profile.2fa_description' => __('Agregue seguridad adicional a su cuenta mediante la autenticación de dos factores.'),
                'profile.2fa_scan_qr_finish' => __('Para terminar de habilitar la autenticación de dos factores, escanee el siguiente código QR usando la aplicación autenticadora de su teléfono o ingrese la clave de configuración y proporcione el código OTP generado.'),
                'profile.2fa_scan_qr_enabled' => __('La autenticación de dos factores ahora está habilitada. Escanee el siguiente código QR usando la aplicación autenticadora de su teléfono o ingrese la clave de configuración.'),
                'profile.2fa_setup_key' => __('Clave de configuración'),
                'profile.2fa_code' => __('Código'),
                'profile.2fa_recovery_store' => __('Almacene estos códigos de recuperación en un administrador de contraseñas seguro. Pueden usarse para recuperar el acceso a su cuenta si se pierde su dispositivo de autenticación de dos factores.'),
                'profile.2fa_enable' => __('Habilitar'),
                'profile.2fa_confirm' => __('Confirmar'),
                'profile.2fa_regenerate_codes' => __('Regenerar códigos de recuperación'),
                'profile.2fa_show_codes' => __('Mostrar códigos de recuperación'),
                'profile.2fa_cancel' => __('Cancelar'),
                'profile.2fa_disable' => __('Deshabilitar'),
                'profile.browser_sessions_title' => __('Sesiones del navegador'),
                'profile.browser_sessions_description' => __('Administre y cierre sesión de sus sesiones activas en otros navegadores y dispositivos.'),
                'Cancel' => __('Cancelar'),
                'Log Out Other Browser Sessions' => __('Cerrar sesión en otros navegadores'),
                'This device' => __('Este dispositivo'),
                'Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.' => __('Por favor ingrese su contraseña para confirmar que desea cerrar sesión en todos sus otros navegadores en todos sus dispositivos.'),
                'profile.delete_title' => __('Eliminar cuenta'),
                'profile.delete_confirm_text' => __('¿Está seguro de que desea eliminar su cuenta? Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. Ingrese su contraseña para confirmar que desea eliminar permanentemente su cuenta.'),
            ],
        ];
    }
}
