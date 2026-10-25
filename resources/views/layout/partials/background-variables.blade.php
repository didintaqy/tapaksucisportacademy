{{--
    Variabel CSS untuk gambar latar yang bisa diubah dari Pengaturan Situs.

    Nilai ini dipakai oleh resources/css/custom.css melalui var(--bg-*, url(...)),
    sehingga admin cukup mengunggah satu gambar dan seluruh section terkait ikut berubah.
--}}
@php
    $bgAssets = [
        '--bg-hero' => 'hero',
        '--bg-page-header' => 'page_header',
        '--bg-footer' => 'footer_bg',
        '--bg-testimonials' => 'testimonials_bg',
        '--bg-dark-section' => 'dark_section_bg',
        '--bg-core-feature' => 'core_feature_bg',
    ];
@endphp
<style>
    :root {
        @foreach ($bgAssets as $variable => $assetKey)
            @php($url = data_get($site, 'assets.'.$assetKey))
            @if (filled($url))
                {{ $variable }}: url('{{ str_replace(["'", '(', ')'], ['%27', '%28', '%29'], $url) }}');
            @endif
        @endforeach
    }
</style>
