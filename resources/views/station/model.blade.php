@extends('layouts.admin')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


@section('main-content')
<div class="container mt-4">
  <h3>รายงานตรวจสอบ</h3>
  
    <iframe 
     style="transform: scale(1.1); transform-origin: top left; width: 100%; height: 600px; border: none;"
     src="https://cbwm.dit.go.th/qr.aspx?id=0-007403-4-3053-004095-68" title="Audit Report" width="100%" allowfullscreen></iframe>
  
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
