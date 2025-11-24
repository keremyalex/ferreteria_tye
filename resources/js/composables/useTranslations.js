import { usePage } from '@inertiajs/vue3'

export function useTranslations() {
    const page = usePage()
    
    const __ = (key, replacements = {}) => {
        let translation = page.props.translations?.[key] || key
        
        // Replace placeholders in the translation
        Object.keys(replacements).forEach(placeholder => {
            translation = translation.replace(`:${placeholder}`, replacements[placeholder])
        })
        
        return translation
    }
    
    return {
        __,
        t: __
    }
}