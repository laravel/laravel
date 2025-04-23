    @php
        $editLocale = $crud->getRequest()->input('_locale', app()->getLocale());
        $fallbackLocale = app()->getFallbackLocale();
        $translatableAttributes = $entry->getTranslatableAttributes();
        $translatableLocales = $crud->model->getAvailableLocales();
        $translatedLocales = [];
        $translatedAttributes = array_filter($translatableAttributes, function($attribute) use ($entry, $editLocale, &$translatedLocales) {
                $translation = $entry->getTranslation($attribute, $editLocale, false) ?? false;
                if($translation) {
                    $translatedLocales[] = $editLocale;
                }
                return $translation;
        });
        $translatedLocales = array_unique($translatedLocales);

        // if translated locales are empty, we need to cycle through all available locales and check if they are translated
        if(empty($translatedLocales)) {
            foreach ($translatableLocales as $locale => $localeName) {
                if($locale === $editLocale) continue;

                array_filter($translatableAttributes, function($attribute) use ($entry, $locale, &$translatedLocales) {
                    $translation = $entry->getTranslation($attribute, $locale, false) ?? false;
                    if($translation) {
                        $translatedLocales[] = $locale;
                    }
                    return $translation;
                });
            }
            $translatedLocales = array_unique($translatedLocales);
        }

        if($crud->getOperationSetting('showTranslationNotice') === false) {
            $showTranslationNotice = false;
        }

        $showTranslationNotice ??= empty($translatedAttributes) && ! empty($entry->getTranslatableAttributes()) && ! $crud->getRequest()->input('_fallback_locale');

        if($showTranslationNotice) {
            $translationNoticeText = trans('backpack::crud.no_attributes_translated', ['locale' => $translatableLocales[$editLocale]]).'<br/>';
            if(count($translatedLocales) === 1) {
                $translationNoticeText .= '<a href="'.url($crud->route.'/'.$entry->getKey().'/edit').'?_locale='.$editLocale.'&_fallback_locale='.current($translatedLocales).'" class="text-white"> > '.trans('backpack::crud.no_attributes_translated_href_text', ['locale' => $translatableLocales[current($translatedLocales)]]).'</a>';
            }else {
                foreach($translatedLocales as $locale) {
                    $translationNoticeText .= '<a href="'.url($crud->route.'/'.$entry->getKey().'/edit').'?_locale='.$editLocale.'&_fallback_locale='.$locale.'" class="text-white"> > '.trans('backpack::crud.no_attributes_translated_href_text', ['locale' => $translatableLocales[$locale]]).' </a><br/>';
                }
            }
        }
    @endphp
<div class="mb-2 text-left text-start">
    <div class="btn-group col-md-2 text-left text-start"  style="margin-top:0.8em; display:inline;">
        <button 
            type="button" 
            class="btn btn-primary dropdown-toggle" 
            data-toggle="dropdown" 
            data-bs-toggle="dropdown" 
            aria-haspopup="true" 
            aria-expanded="false">
            {{trans('backpack::crud.language')}}: {{ $crud->model->getAvailableLocales()[$editLocale] }} &nbsp; <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
        @foreach ($crud->model->getAvailableLocales() as $key => $locale)
            <a 
                class="dropdown-item" 
                href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}?_locale={{ $key }}">
                {{ $locale }}
            </a>
        @endforeach
        </ul>
    </div>
</div>

@push('after_scripts')
    <script>
       document.addEventListener("DOMContentLoaded", () => {
            let showTranslationNotice = @json($showTranslationNotice);

            if(!showTranslationNotice) return;

            let translationNoticeText = @json($translationNoticeText ?? false);
            
            new Noty({
                type: 'info',
                text: translationNoticeText,
                layout: 'topRight',
                timeout: false,
                progressBar: false,
                closeWith: ['button'],
            }).show();
        });
    </script>
@endpush
