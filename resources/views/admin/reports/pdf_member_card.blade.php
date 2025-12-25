<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Member Card</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
            padding: 0px;
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #fff;
        }

        /* --- CONTAINER UTAMA --- */
        .card {
            width: 242pt;
            height: 153pt;
            position: relative;
            background-color: #fff;
            overflow: hidden;
        }

        /* --- 1. JAHITAN PUTUS-PUTUS (Sama untuk semua) --- */
        .stitch-border {
            position: absolute;
            top: 6pt;
            left: 6pt;
            right: 6pt;
            bottom: 6pt;
            border-width: 2px;
            border-style: dashed;
            border-radius: 10px;
            z-index: 10;
            pointer-events: none;
        }

        /* --- 2. BACKGROUND SHAPES (Lingkaran) --- */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            z-index: 0;
        }

        /* Posisi Shape */
        .shape-1 {
            width: 120pt;
            height: 120pt;
            bottom: -40pt;
            right: -30pt;
        }

        .shape-2 {
            width: 80pt;
            height: 80pt;
            top: -30pt;
            left: -20pt;
        }

        /* --- 3. ORNAMEN SIMBOL --- */
        .ornament {
            position: absolute;
            z-index: 1;
            font-size: 14pt;
        }

        /* Posisi Ornamen */
        .orn-1 {
            top: 15pt;
            right: 85pt;
            font-size: 10pt;
        }

        .orn-2 {
            bottom: 20pt;
            left: 20pt;
            font-size: 18pt;
        }

        .orn-3 {
            top: 50pt;
            right: 10pt;
            font-size: 12pt;
            transform: rotate(45deg);
        }

        .orn-4 {
            bottom: 50pt;
            right: 20pt;
            font-size: 16pt;
        }

        /* --- HEADER & TEXT --- */
        .header {
            position: absolute;
            top: 15pt;
            right: 20pt;
            text-align: right;
            z-index: 2;
        }

        .header-title {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header-sub {
            font-size: 5pt;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 2px;
            font-weight: bold;
        }

        /* --- FOTO --- */
        .photo-area {
            position: absolute;
            top: 40pt;
            left: 20pt;
            width: 55pt;
            z-index: 5;
            text-align: center;
        }

        .photo-frame {
            width: 50pt;
            height: 50pt;
            background: #fff;
            padding: 3pt;
            border-width: 2px;
            border-style: solid;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .role-tag {
            font-size: 5pt;
            padding: 2px 8px;
            border-radius: 10px;
            margin-top: -8pt;
            position: relative;
            z-index: 6;
            display: inline-block;
            color: #fff;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }

        /* --- CONTENT --- */
        .content {
            position: absolute;
            top: 55pt;
            left: 85pt;
            right: 15pt;
            z-index: 5;
        }

        .content-no-photo {
            position: absolute;
            top: 50pt;
            left: 25pt;
            right: 25pt;
            z-index: 5;
        }

        .name {
            font-size: 14pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4pt;
            text-transform: uppercase;
        }

        .dept-badge {
            display: inline-block;
            font-size: 7pt;
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: bold;
            border: 1px solid;
        }

        /* --- FOOTER --- */
        .footer {
            position: absolute;
            bottom: 15pt;
            left: 25pt;
            z-index: 5;
        }

        .nim-label {
            font-size: 5pt;
            color: #94a3b8;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .nim-value {
            font-size: 11pt;
            color: #475569;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
            background: #fff;
            padding: 0 4px;
        }


        /* ========================================= */
        /* TEMA WARNA (THEMING)             */
        /* ========================================= */

        /* --- TEMA CEWEK (GIRL) - Persis yang Anda suka --- */
        .theme-girl .stitch-border {
            border-color: #f9a8d4;
        }

        /* Pink muda */

        .theme-girl .shape-1 {
            background-color: #fce7f3;
        }

        /* Pink pastel */
        .theme-girl .shape-2 {
            background-color: #e0f2fe;
        }

        /* Biru pastel */

        .theme-girl .header-title {
            color: #1e293b;
        }

        .theme-girl .header-title span {
            color: #f472b6;
        }

        .theme-girl .header-sub {
            background-color: #fef3c7;
            color: #d97706;
        }

        .theme-girl .photo-frame {
            border-color: #f9a8d4;
        }

        .theme-girl .role-tag {
            background: #f472b6;
        }

        .theme-girl .dept-badge {
            background-color: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .theme-girl .orn-color-1 {
            color: #f472b6;
        }

        /* Pink */
        .theme-girl .orn-color-2 {
            color: #fcd34d;
        }

        /* Kuning */
        .theme-girl .orn-color-3 {
            color: #60a5fa;
        }

        /* Biru */


        /* --- TEMA COWOK (BOY) - Versi Maskulin --- */
        .theme-boy .stitch-border {
            border-color: #7dd3fc;
        }

        /* Biru Langit */

        .theme-boy .shape-1 {
            background-color: #e0f2fe;
        }

        /* Biru muda */
        .theme-boy .shape-2 {
            background-color: #f1f5f9;
        }

        /* Abu muda */

        .theme-boy .header-title {
            color: #0f172a;
        }

        .theme-boy .header-title span {
            color: #3b82f6;
        }

        /* Biru Strong */
        .theme-boy .header-sub {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .theme-boy .photo-frame {
            border-color: #38bdf8;
        }

        .theme-boy .role-tag {
            background: #0ea5e9;
        }

        .theme-boy .dept-badge {
            background-color: #f0f9ff;
            color: #0369a1;
            border-color: #7dd3fc;
        }

        .theme-boy .orn-color-1 {
            color: #3b82f6;
        }

        /* Biru */
        .theme-boy .orn-color-2 {
            color: #fbbf24;
        }

        /* Amber */
        .theme-boy .orn-color-3 {
            color: #94a3b8;
        }

        /* Slate */
    </style>
</head>

<body>

    @php
    // LOGIKA: Cek Gender
    // Jika 'L' / 'Male' -> Boy Theme. Selain itu -> Girl Theme.
    $isBoy = in_array(strtoupper($student->gender), ['L', 'MALE', 'LAKI-LAKI', 'PRIA']);

    $themeClass = $isBoy ? 'theme-boy' : 'theme-girl';
    $hasPhoto = !empty($student->photo);
    @endphp

    <div class="card {{ $themeClass }}">

        <div class="stitch-border"></div>

        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>

        @if($isBoy)
        <div class="ornament orn-1 orn-color-1">★</div>
        <div class="ornament orn-2 orn-color-2">✦</div>
        <div class="ornament orn-3 orn-color-1">♦</div>
        <div class="ornament orn-4 orn-color-3">★</div>
        @else
        <div class="ornament orn-1 orn-color-1">♥</div>
        <div class="ornament orn-2 orn-color-2">★</div>
        <div class="ornament orn-3 orn-color-2">★</div>
        <div class="ornament orn-4 orn-color-3">✦</div>
        @endif


        <div class="header">
            <div class="header-title">Perpus<span>Kampus</span></div>
            <div class="header-sub">MEMBER CARD</div>
        </div>

        @if($hasPhoto)
        <div class="photo-area">
            <div class="photo-frame">
                <img src="{{ public_path('storage/' . $student->photo) }}">
            </div>
            <div class="role-tag">STUDENT</div>
        </div>
        @endif

        <div class="{{ $hasPhoto ? 'content' : 'content-no-photo' }}">
            <div class="name">{{ Str::limit($student->name, 20) }}</div>
            <div class="dept-badge">{{ $student->jurusan }}</div>
        </div>

        <div class="footer">
            <div class="nim-label">IDENTITY NUMBER</div>
            <div class="nim-value">{{ $student->nim }}</div>
        </div>

    </div>

</body>

</html>