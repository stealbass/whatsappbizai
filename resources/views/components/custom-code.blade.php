@php $site = \App\Models\SiteSetting::instance(); @endphp
@if($site->custom_head_css)
<style>{!! $site->custom_head_css !!}</style>
@endif
@if($site->custom_head_js)
<script>{!! $site->custom_head_js !!}</script>
@endif
