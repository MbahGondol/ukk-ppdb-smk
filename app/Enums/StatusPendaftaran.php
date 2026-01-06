<?php

namespace App\Enums;

enum StatusPendaftaran: string
{
    case DRAFT = 'Draft';
    case MELENGKAPI_BERKAS = 'Melengkapi Berkas';
    case TERDAFTAR = 'Terdaftar';
    case DITERIMA = 'Resmi Diterima';
    case DITOLAK = 'Ditolak';
}