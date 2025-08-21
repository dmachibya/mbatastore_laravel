<!-- LinkedIn Insight Tag Code -->
@if(config('services.linkedin.partner_id'))
<script type="text/javascript">
    _linkedin_partner_id = "{{ config('services.linkedin.partner_id') }}";
    window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
    window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script>
<script type="text/javascript">
    (function() {
        var s = document.getElementsByTagName("script")[0];
        var b = document.createElement("script");
        b.type = "text/javascript";
        b.async = true;
        b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
        s.parentNode.insertBefore(b, s);
    })();
</script>
<noscript>
  <img height="1" width="1" style="display:none;" alt="" src="https://dc.ads.linkedin.com/collect/?pid='{{ config('services.linkedin.partner_id') }}'&fmt=gif" />
</noscript>
@endif