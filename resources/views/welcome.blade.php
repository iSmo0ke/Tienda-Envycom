<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>ENVYCOM</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>

body{
background:#f5f6f8;
font-family: 'Segoe UI', sans-serif;
}

.navbar{
background:#0c2b45;
}

.logo{
height:40px;
}

.btn-envy{
background:#d7ff00;
border:none;
font-weight:600;
}

.product-card{
border-radius:12px;
box-shadow:0 2px 6px rgba(0,0,0,.08);
padding:15px;
background:white;
}

.product-card img{
height:120px;
object-fit:contain;
}

.section-title{
font-weight:700;
margin-top:60px;
text-align:center;
}

.service-card{
background:white;
border-radius:12px;
padding:20px;
box-shadow:0 2px 5px rgba(0,0,0,.08);
text-align:center;
}

.brand-card{
background:white;
padding:20px;
border-radius:10px;
text-align:center;
box-shadow:0 2px 5px rgba(0,0,0,.08);
}

.team-card{
background:white;
border-radius:12px;
padding:20px;
text-align:center;
box-shadow:0 2px 5px rgba(0,0,0,.1);
}

.team-card img{
width:80px;
height:80px;
border-radius:50%;
}

</style>

</head>
@extends('layouts.app')
<body>

<!-- PRODUCTOS -->

<h3 class="mb-4">PRODUCTOS</h3>
<p>Encuentra lo mejor en tecnología</p>

<div class="row g-4">

@foreach(range(1,6) as $producto)

<div class="col-md-2">

<div class="product-card text-center">

<img src="https://via.placeholder.com/150">

<h6 class="mt-3">DELL</h6>

<p class="small">Laptop i5 8GB</p>

<h5>$7,949.00</h5>

<button class="btn btn-envy w-100">
Agregar
</button>

</div>

</div>

@endforeach

</div>


<!-- SERVICIOS -->

<h2 class="section-title">SERVICIOS</h2>

<div class="row mt-4 g-4">

<div class="col-md-2">
<div class="service-card">

<p>Equipo de Cómputo</p>
</div>
</div>

<div class="col-md-2">
<div class="service-card">

<p>Impresión</p>
</div>
</div>

<div class="col-md-2">
<div class="service-card">

<p>Software</p>
</div>
</div>

<div class="col-md-2">
<div class="service-card">

<p>Redes</p>
</div>
</div>

<div class="col-md-2">
<div class="service-card">

<p>Mantenimiento</p>
</div>
</div>

</div>


<!-- MARCAS -->

<h2 class="section-title">NUESTRAS MARCAS</h2>

<div class="row mt-4 g-4 text-center">

@foreach(['Dell','HP','Microsoft','Cisco','Asus','Benq','Apple'] as $marca)

<div class="col-md-2">

<div class="brand-card">

{{ $marca }}

</div>

</div>

@endforeach

</div>


<!-- EQUIPO -->

<h2 class="section-title">EQUIPO DE TRABAJO</h2>

<div class="row mt-4 g-4">

<div class="col-md-3">
<div class="team-card">

<img src="https://i.pravatar.cc/100">

<h5 class="mt-3">Jesús Altamirano</h5>
<p>Ingeniero en T.I</p>

</div>
</div>


<div class="col-md-3">
<div class="team-card">

<img src="https://i.pravatar.cc/101">

<h5 class="mt-3">Maria Gómez</h5>
<p>Ventas y soporte</p>

</div>
</div>


<div class="col-md-3">
<div class="team-card">

<img src="https://i.pravatar.cc/102">

<h5 class="mt-3">Carlos Ruiz</h5>
<p>Consultor IT</p>

</div>
</div>


<div class="col-md-3">
<div class="team-card">

<img src="https://i.pravatar.cc/103">

<h5 class="mt-3">Ana López</h5>
<p>Administracin</p>

</div>
</div>

</div>

</div>

</body>
</html>