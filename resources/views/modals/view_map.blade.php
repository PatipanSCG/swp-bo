<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <iframe width="100%" height="400" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}">
</script>