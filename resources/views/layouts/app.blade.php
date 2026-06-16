@php
    $logoPath = \App\Models\Setting::getVal('logo_path', '/logo-placeholder.png');
    $logoPosition = \App\Models\Setting::getVal('logo_position', 'sidebar_top');
    $logoX = \App\Models\Setting::getVal('logo_x', '20px');
    $logoY = \App\Models\Setting::getVal('logo_y', '20px');
    $user = Auth::user();
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::getVal('system_title', 'Plataforma Corporativa RH') }} - Vacaciones e Incidencias</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Vacaciones RH">
    <link rel="apple-touch-icon" href="{{ $logoPath }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: #f1f5f9;
            color: #334155;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Glassmorphism & Modern Colors */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: #0f172a;
            color: #94a3b8;
            padding: 24px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
            border-right: 1px solid #1e293b;
            z-index: 100;
            overflow-y: auto;
        }

        /* Elegante barra de desplazamiento para el sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
            padding: 20px 15px;
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .logo-img {
            max-width: 150px;
            max-height: 80px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .logo-text {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.6px;
            background: linear-gradient(135deg, #38bdf8 0%, #60a5fa 50%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            display: block;
            width: 100%;
            line-height: 1.4;
            filter: drop-shadow(0 2px 8px rgba(56, 189, 248, 0.3));
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        /* Buscador del Sidebar */
        .sidebar-search {
            margin-bottom: 15px;
            position: relative;
        }

        .sidebar-search input {
            width: 100%;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 10px 14px 10px 38px;
            font-size: 13px;
            color: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }

        .sidebar-search input:focus {
            background: #1e293b;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .sidebar-search svg.search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        /* Categorías Colapsables (Menús Principales) */
        .menu-category-wrapper {
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .menu-category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #cbd5e1;
            font-weight: 600;
            margin-top: 8px;
            margin-bottom: 6px;
            padding: 12px 16px;
            cursor: pointer;
            border-radius: 10px;
            user-select: none;
            background: #1e293b;
            border: 1px solid #334155;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-category-header:hover {
            color: #f8fafc;
            background: #2e3b4e;
            border-color: #475569;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        .menu-category-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-category-header svg.chevron-icon {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.5;
            transition: transform 0.2s ease;
        }

        .menu-category-content {
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.2s ease;
            max-height: 500px;
            opacity: 1;
            padding: 8px 0 4px 14px;
            margin-left: 14px;
            border-left: 1px dashed #334155;
        }

        .menu-category-wrapper.collapsed .menu-category-content {
            max-height: 0;
            opacity: 0;
            padding-top: 0;
            padding-bottom: 0;
            pointer-events: none;
        }

        .menu-category-wrapper.collapsed .menu-category-header svg.chevron-icon {
            transform: rotate(-90deg);
        }

        /* Enlaces del Menú (Módulos Internos) */
        .menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            color: #94a3b8;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .menu a svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.2s ease;
        }

        /* Colores por módulo - Inactivo */
        /* Dashboard (Blue) */
        .menu a:not(.active)[href="/"] {
            background: rgba(37, 99, 235, 0.05);
            border-color: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
        }
        .menu a:not(.active)[href="/"] svg { stroke: #60a5fa; }

        /* Mi Sitio (Cyan) */
        .menu a:not(.active)[href*="/mi-sitio"] {
            background: rgba(14, 165, 233, 0.05);
            border-color: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
        }
        .menu a:not(.active)[href*="/mi-sitio"] svg { stroke: #38bdf8; }

        /* Empleados (Blue) */
        .menu a:not(.active)[href*="/empleados"] {
            background: rgba(37, 99, 235, 0.05);
            border-color: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
        }
        .menu a:not(.active)[href*="/empleados"] svg { stroke: #60a5fa; }

        /* Vacaciones (Green) */
        .menu a:not(.active)[href*="/vacaciones"] {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .menu a:not(.active)[href*="/vacaciones"] svg { stroke: #34d399; }

        /* Incidencias (Red) */
        .menu a:not(.active)[href*="/incidencias"]:not([href*="reporte"]) {
            background: rgba(244, 63, 94, 0.05);
            border-color: rgba(244, 63, 94, 0.15);
            color: #fb7185;
        }
        .menu a:not(.active)[href*="/incidencias"]:not([href*="reporte"]) svg { stroke: #fb7185; }

        /* Movimientos (Green) */
        .menu a:not(.active)[href*="/movimientos"] {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .menu a:not(.active)[href*="/movimientos"] svg { stroke: #34d399; }

        /* Reporte (Orange) */
        .menu a:not(.active)[href*="/incidencias/reporte"] {
            background: rgba(245, 158, 11, 0.05);
            border-color: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }
        .menu a:not(.active)[href*="/incidencias/reporte"] svg { stroke: #fbbf24; }

        /* Reportes de Vacaciones (Green) */
        .menu a:not(.active)[href*="/reportes/vacaciones-"] {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .menu a:not(.active)[href*="/reportes/vacaciones-"] svg { stroke: #34d399; }

        /* Usuarios (Purple) */
        .menu a:not(.active)[href*="/usuarios"] {
            background: rgba(139, 92, 246, 0.05);
            border-color: rgba(139, 92, 246, 0.15);
            color: #c084fc;
        }
        .menu a:not(.active)[href*="/usuarios"] svg { stroke: #c084fc; }

        /* Mi Cuenta (Indigo) */
        .menu a:not(.active)[href*="/mi-cuenta"] {
            background: rgba(99, 102, 241, 0.05);
            border-color: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }
        .menu a:not(.active)[href*="/mi-cuenta"] svg { stroke: #818cf8; }

        /* Configuracion (Gray/Slate) */
        .menu a:not(.active)[href*="/configuracion"] {
            background: rgba(100, 116, 139, 0.05);
            border-color: rgba(100, 116, 139, 0.15);
            color: #cbd5e1;
        }
        .menu a:not(.active)[href*="/configuracion"] svg { stroke: #cbd5e1; }

        /* Hovers - Inactivos */
        .menu a:not(.active):hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        .menu a:not(.active):hover[href="/"] {
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .menu a:not(.active):hover[href*="/mi-sitio"] {
            background: rgba(14, 165, 233, 0.1);
            border-color: rgba(14, 165, 233, 0.3);
        }
        .menu a:not(.active):hover[href*="/empleados"] {
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .menu a:not(.active):hover[href*="/vacaciones"] {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .menu a:not(.active):hover[href*="/incidencias"]:not([href*="reporte"]) {
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.3);
        }
        .menu a:not(.active):hover[href*="/movimientos"] {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .menu a:not(.active):hover[href*="/incidencias/reporte"] {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
        }
        .menu a:not(.active):hover[href*="/reportes/vacaciones-"] {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .menu a:not(.active):hover[href*="/usuarios"] {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.3);
        }
        .menu a:not(.active):hover[href*="/mi-cuenta"] {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .menu a:not(.active):hover[href*="/configuracion"] {
            background: rgba(100, 116, 139, 0.1);
            border-color: rgba(100, 116, 139, 0.3);
        }

        /* Activos */
        .menu a.active[href="/"] {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border-color: #2563eb !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
        }
        .menu a.active[href*="/mi-sitio"] {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            border-color: #0284c7 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2) !important;
        }
        .menu a.active[href*="/empleados"] {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border-color: #2563eb !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
        }
        .menu a.active[href*="/vacaciones"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #059669 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }
        .menu a.active[href*="/incidencias"]:not([href*="reporte"]) {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            border-color: #dc2626 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
        }
        .menu a.active[href*="/movimientos"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #059669 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }
        .menu a.active[href*="/incidencias/reporte"] {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border-color: #d97706 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2) !important;
        }
        .menu a.active[href*="/reportes/vacaciones-"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-color: #059669 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }
        .menu a.active[href*="/usuarios"] {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
            border-color: #6d28d9 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2) !important;
        }
        .menu a.active[href*="/mi-cuenta"] {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            border-color: #4f46e5 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2) !important;
        }
        .menu a.active[href*="/configuracion"] {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            border-color: #475569 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.2) !important;
        }

        .menu a.active svg {
            stroke: white !important;
        }

        /* Etiquetas Próximamente / Deshabilitados */
        .badge-coming-soon {
            background: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            font-size: 9px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .menu a.disabled-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .menu a.disabled-link:hover {
            background: transparent;
            transform: none;
            color: #64748b;
        }

        /* Contenido Principal */
        .contenido {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 40px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Topbar Premium */
        .topbar {
            background: #ffffff;
            padding: 16px 28px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        /* Elementos del menú responsivo */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.2s;
            margin-right: 15px;
        }

        .sidebar-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .sidebar-toggle svg {
            width: 24px;
            height: 24px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 90;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 992px) {
            .sidebar {
                left: -280px;
                transition: left 0.3s ease;
            }

            .sidebar.open {
                left: 0;
            }

            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .contenido {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-logout {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        /* Cartas y Formas Premium */
        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            flex-grow: 1;
            color: #334155;
        }

        .card h1, .card h2, .card h3, .card h4, .card h5, .card h6 {
            color: #0f172a !important;
        }

        .card p {
            color: #475569;
        }

        .card small, .card .text-muted, .card p.text-muted, .card span.text-muted {
            color: #64748b !important;
        }

        /* Botones Generales */
        .boton {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 11px 22px;
            text-decoration: none;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13.5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .boton:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .boton-volver {
            background: #f1f5f9;
            color: #475569;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13.5px;
            margin-bottom: 25px;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
        }

        .boton-volver:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Formularios */
        .grupo {
            margin-bottom: 20px;
        }

        .grupo label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
            color: #64748b;
        }

        .grupo input, .grupo select, .grupo textarea {
            width: 100%;
            padding: 11px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .grupo input:focus, .grupo select:focus, .grupo textarea:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        /* General styles for selects outside forms (like filters) to ensure high contrast */
        select {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
        }

        /* Forms select inside .grupo overrides the general white select style */
        .grupo select {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
        }

        /* Option styling for general selects and form selects */
        select option {
            background-color: #ffffff !important;
            color: #1e293b !important;
        }

        .grupo select option {
            background-color: #ffffff !important;
            color: #1e293b !important;
        }

        /* Tablas Modernas */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13.5px;
            color: #334155;
            background: #ffffff;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) td {
            background-color: #f8fafc; /* Filas alternadas */
        }

        tr:hover td {
            background: #f1f5f9 !important; /* Resaltado hover suave */
        }

        tbody tr.selected-row td {
            background-color: #2563eb !important; /* Resaltado de selección */
            color: #ffffff !important;
            font-weight: 500;
        }

        tbody tr.selected-row td,
        tbody tr.selected-row td *:not(.badge-estado),
        tbody tr.selected-row td span:not(.badge-estado),
        tbody tr.selected-row td strong,
        tbody tr.selected-row td a,
        tbody tr.selected-row td div:not(.badge-estado) {
            color: #ffffff !important;
        }

        tbody tr.selected-row td a svg {
            stroke: #ffffff !important;
        }

        /* Alertas Premium */
        .alerta-success {
            background: #d1fae5;
            color: #065f46;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #a7f3d0;
            font-weight: 500;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alerta-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #fca5a5;
            font-weight: 500;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Logo Arrastrable */
        .draggable-logo {
            position: fixed;
            cursor: move;
            z-index: 9999;
            padding: 8px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border-radius: 12px;
            border: 1.5px dashed #3b82f6;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
            backdrop-filter: blur(8px);
        }

        .draggable-logo img {
            max-width: 140px;
            max-height: 70px;
            object-fit: contain;
            pointer-events: none;
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
                font-size: 10px !important;
            }
            .sidebar, .topbar, .boton, .boton-volver, .btn-danger-solid, .btn-danger-outline, .import-container, .barra-acciones, .seccion-eliminar-sitio, .no-print, input[type="text"], select {
                display: none !important;
            }
            .contenido {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            table {
                border: 1px solid #cbd5e1 !important;
                width: 100% !important;
                table-layout: auto !important;
                font-size: 9px !important;
            }
            th, td {
                color: black !important;
                border-bottom: 1px solid #cbd5e1 !important;
                padding: 4px 6px !important;
                word-wrap: break-word !important;
                white-space: normal !important;
            }
            table form, table button, table a {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                color: black !important;
                padding: 0 !important;
                text-decoration: none !important;
                font-weight: bold !important;
                font-size: 9px !important;
                display: inline !important;
            }
        }

        /* Mobile Responsive Enhancements */
        @media (max-width: 992px) {
            .topbar {
                padding: 12px 16px;
                margin-bottom: 20px;
            }
            .user-info {
                display: none !important;
            }
            .btn-logout {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr !important;
            }
            /* Make action bars with inline flex styles stack vertically on mobile */
            .contenido > div[style*="display:flex"],
            .card > div[style*="display:flex"],
            .card > form > div[style*="display:flex"] {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 15px !important;
            }
            .contenido > div[style*="display:flex"] > div,
            .card > div[style*="display:flex"] > div,
            .card > form > div[style*="display:flex"] > div {
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 8px !important;
            }
            .contenido > div[style*="display:flex"] input,
            .card > div[style*="display:flex"] input,
            .card > form > div[style*="display:flex"] input {
                width: 100% !important;
            }
            .boton {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .contenido {
                padding: 10px !important;
            }
            .card {
                padding: 15px !important;
                border-radius: 12px !important;
            }
            .topbar {
                padding: 10px !important;
                border-radius: 12px !important;
                margin-bottom: 15px !important;
            }
            .topbar-title {
                font-size: 14px !important;
            }
        }

        /* Responsive Tables Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .table-responsive table {
            margin-top: 0 !important;
            border: none !important;
        }
    </style>
</head>

<body>

    <!-- Logo Arrastrable Floating -->
    @if($logoPosition === 'draggable')
        <div id="draggableLogo" class="draggable-logo" style="left: {{ $logoX }}; top: {{ $logoY }};">
            <img src="{{ $logoPath }}" alt="Logo Corporativo">
        </div>
    @endif

    <!-- Overlay para el sidebar móvil -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar Menu -->
    <div class="sidebar">
        <div class="logo-container">
            @if($logoPosition === 'sidebar_top')
                <img src="{{ $logoPath }}" alt="Logo Corporativo" class="logo-img">
            @endif
            <span class="logo-text">{{ \App\Models\Setting::getVal('system_title', 'Plataforma Corporativa RH') }}</span>
        </div>

        @if($user)
            <!-- Buscador en Sidebar -->
            <div class="sidebar-search">
                <svg class="search-icon" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="menuSearch" placeholder="Buscar módulo..." aria-label="Buscar módulo">
            </div>
        @endif

        <div class="menu">
            @if($user)
                <!-- Inicio (Siempre visible) -->
                <div class="menu-category-wrapper" id="cat-inicio">
                    <div class="menu-category-header" style="cursor: default; pointer-events: none;">
                        <div class="menu-category-header-left">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Inicio</span>
                        </div>
                    </div>
                    <div class="menu-category-content">
                        <a href="/" class="{{ Request::is('/') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9"></rect>
                                <rect x="14" y="3" width="7" height="5"></rect>
                                <rect x="14" y="12" width="7" height="9"></rect>
                                <rect x="3" y="16" width="7" height="5"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        
                        @if(!$user->esAdmin() && !$user->esSupervisor())
                            <a href="/mi-sitio" class="{{ Request::is('mi-sitio*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Mi Sitio Personal</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Recursos Humanos (Colapsable) -->
                <div class="menu-category-wrapper" id="cat-rh">
                    <div class="menu-category-header" onclick="toggleCategory('cat-rh')">
                        <div class="menu-category-header-left">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            <span>Recursos Humanos</span>
                        </div>
                        <svg class="chevron-icon" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="menu-category-content">
                        <a href="/empleados" class="{{ Request::is('empleados*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Empleados</span>
                        </a>

                        <a href="/vacaciones" class="{{ Request::is('vacaciones*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.07" x2="5.64" y2="17.66"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <span>Saldos Vacaciones</span>
                        </a>

                        <a href="/movimientos" class="{{ Request::is('movimientos*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Movimientos</span>
                        </a>

                        <a href="/incidencias" class="{{ Request::is('incidencias*') && !Request::is('incidencias/reporte*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span>Incidencias</span>
                        </a>
                    </div>
                </div>

                <!-- Reportes (Colapsable) -->
                <div class="menu-category-wrapper" id="cat-reports">
                    <div class="menu-category-header" onclick="toggleCategory('cat-reports')">
                        <div class="menu-category-header-left">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            <span>Reportes</span>
                        </div>
                        <svg class="chevron-icon" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="menu-category-content">
                        <a href="/incidencias/reporte" class="{{ Request::is('incidencias/reporte*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Desempeño</span>
                        </a>

                        <a href="/reportes/vacaciones-general" class="{{ Request::is('reportes/vacaciones-general*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.07" x2="5.64" y2="17.66"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <span>General Vacaciones</span>
                        </a>

                        <a href="/reportes/vacaciones-detalle" class="{{ Request::is('reportes/vacaciones-detalle*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="12" y1="10" x2="12" y2="20"></line>
                                <line x1="12" y1="15" x2="18" y2="15"></line>
                            </svg>
                            <span>Detalle de Cálculo</span>
                        </a>
                    </div>
                </div>

                    <!-- Administración (Colapsable) -->
                    <div class="menu-category-wrapper" id="cat-admin">
                        <div class="menu-category-header" onclick="toggleCategory('cat-admin')">
                            <div class="menu-category-header-left">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                <span>Administración</span>
                            </div>
                            <svg class="chevron-icon" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <div class="menu-category-content">
                            <a href="/mi-cuenta" class="{{ Request::is('mi-cuenta*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Mi Cuenta</span>
                            </a>

                            @if($user->esAdmin())
                            <a href="/usuarios" class="{{ Request::is('usuarios*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span>Usuarios y Roles</span>
                            </a>
                            @endif
                            
                            @if($user->esAdmin() || $user->esSupervisor())
                            <a href="/configuracion" class="{{ Request::is('configuracion') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Configuración</span>
                            </a>
                            @endif

                            @if($user->esAdmin())
                            <a href="/configuracion/festivos" class="{{ Request::is('configuracion/festivos*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <circle cx="12" cy="16" r="2"></circle>
                                </svg>
                                <span>Días Festivos</span>
                            </a>
                            @endif
                        </div>
                    </div>
            @endif
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="contenido">
        
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-title">
                <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                @if($logoPosition === 'topbar_left')
                    <img src="{{ $logoPath }}" alt="Logo Corporativo" style="max-height: 40px; object-fit: contain; margin-right: 10px;">
                @endif
                Portal de Recursos Humanos
            </div>
            
            <div class="topbar-right">
                @if($logoPosition === 'topbar_right')
                    <img src="{{ $logoPath }}" alt="Logo" style="max-height: 40px; object-fit: contain; margin-right: 15px;">
                @endif

                @if($user)
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @php
                            $words = explode(' ', $user->name);
                            $initials = '';
                            if (count($words) >= 2) {
                                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($user->name, 0, 2));
                            }
                        @endphp
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; border: 1px solid #dbeafe; box-shadow: 0 2px 5px rgba(37, 99, 235, 0.08);" title="{{ $user->name }}">
                            {{ $initials }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ $user->name }}</span>
                            <span class="user-role">
                                @if($user->role === 'administrador')
                                    Administrador
                                @elseif($user->role === 'supervisor')
                                    Supervisor
                                @elseif($user->role === 'solo_lectura')
                                    Solo Visualización
                                @else
                                    Empleado
                                @endif
                            </span>
                        </div>
                    </div>
                    <form action="/logout" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout">Cerrar Sesión</button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alerta-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alerta-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alerta-error">
                <ul style="list-style-type: none; margin: 0; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Cuerpo de la vista -->
        <div class="card">
            @yield('contenido')
        </div>
    </div>

    <!-- Script de Arrastre de Logo y Guardado -->
    @if($logoPosition === 'draggable')
        <script>
            const dragLogo = document.getElementById('draggableLogo');
            let isDragging = false;
            let currentX;
            let currentY;
            let initialX;
            let initialY;
            let xOffset = 0;
            let yOffset = 0;

            // Obtener offsets iniciales de las propiedades inline style si existen
            const styleLeft = dragLogo.style.left;
            const styleTop = dragLogo.style.top;
            
            dragLogo.addEventListener('mousedown', dragStart);
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', dragEnd);

            function dragStart(e) {
                initialX = e.clientX - parseFloat(dragLogo.style.left || 0);
                initialY = e.clientY - parseFloat(dragLogo.style.top || 0);
                
                isDragging = true;
            }

            function drag(e) {
                if (isDragging) {
                    e.preventDefault();
                    currentX = e.clientX - initialX;
                    currentY = e.clientY - initialY;

                    dragLogo.style.left = currentX + 'px';
                    dragLogo.style.top = currentY + 'px';
                }
            }

            function dragEnd() {
                if (!isDragging) return;
                
                isDragging = false;
                
                // Guardar la posición en la base de datos vía AJAX
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('/configuracion/posicion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        logo_position: 'draggable',
                        logo_x: dragLogo.style.left,
                        logo_y: dragLogo.style.top
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Posición del logo guardada correctamente');
                })
                .catch(err => {
                    console.error('Error al guardar la posición del logo:', err);
                });
            }
        </script>
    @endif

    <!-- Script para Interactividad del Sidebar (Toggle móvil, Colapsables con persistencia y Buscador) -->
    <script>
        // Función global para colapsar/expandir categorías
        function toggleCategory(catId) {
            const wrapper = document.getElementById(catId);
            if (wrapper) {
                const isCollapsed = wrapper.classList.toggle('collapsed');
                localStorage.setItem('sidebar_cat_' + catId, isCollapsed ? 'collapsed' : 'expanded');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Toggle del Sidebar en móviles
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar && overlay) {
                function toggleSidebar() {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                }

                toggleBtn.addEventListener('click', toggleSidebar);
                overlay.addEventListener('click', toggleSidebar);
            }

            // 2. Restaurar y configurar estado de categorías colapsables
            const categories = ['cat-rh', 'cat-reports', 'cat-admin'];
            categories.forEach(catId => {
                const wrapper = document.getElementById(catId);
                if (wrapper) {
                    // Si la sección contiene el enlace actualmente activo, mantener abierta obligatoriamente
                    const hasActive = wrapper.querySelector('a.active');
                    if (hasActive) {
                        wrapper.classList.remove('collapsed');
                        return;
                    }

                    // En caso contrario, restaurar desde localStorage
                    const savedState = localStorage.getItem('sidebar_cat_' + catId);
                    if (savedState === 'collapsed') {
                        wrapper.classList.add('collapsed');
                    } else {
                        wrapper.classList.remove('collapsed'); // Por defecto expandidas
                    }
                }
            });

            // 3. Filtrado y búsqueda en tiempo real del menú
            const menuSearch = document.getElementById('menuSearch');
            if (menuSearch) {
                menuSearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    const wrappers = document.querySelectorAll('.menu-category-wrapper');

                    wrappers.forEach(wrapper => {
                        let wrapperHasMatches = false;
                        const links = wrapper.querySelectorAll('.menu-category-content a');

                        links.forEach(link => {
                            const label = link.querySelector('span').textContent.toLowerCase();
                            if (label.includes(query)) {
                                link.style.display = 'flex';
                                wrapperHasMatches = true;
                            } else {
                                link.style.display = 'none';
                            }
                        });

                        // Mostrar u ocultar la sección entera según si tiene coincidencias
                        if (wrapperHasMatches || query === '') {
                            wrapper.style.display = 'block';
                            
                            // Si el usuario está buscando, expandir la categoría automáticamente
                            if (query !== '' && wrapper.id !== 'cat-inicio') {
                                wrapper.classList.remove('collapsed');
                            } else if (query === '' && wrapper.id !== 'cat-inicio') {
                                // Al borrar el buscador, restaurar su estado colapsado guardado
                                const savedState = localStorage.getItem('sidebar_cat_' + wrapper.id);
                                if (savedState === 'collapsed') {
                                    wrapper.classList.add('collapsed');
                                } else {
                                    wrapper.classList.remove('collapsed');
                                }
                            }
                        } else {
                            wrapper.style.display = 'none';
                        }
                    });
                });
            }

            // 4. Selección global de filas en tablas
            document.addEventListener('click', function(e) {
                const tr = e.target.closest('table tbody tr');
                if (tr) {
                    // Ignorar si es una fila de mensaje vacío (colspan)
                    if (tr.cells.length === 1 && tr.cells[0].hasAttribute('colspan')) {
                        return;
                    }
                    
                    const tbody = tr.parentNode;
                    tbody.querySelectorAll('tr').forEach(row => {
                        row.classList.remove('selected-row');
                    });
                    
                    tr.classList.add('selected-row');
                }
            });

            // 5. Envoltura automática de tablas para visualización móvil responsiva
            document.querySelectorAll('table').forEach(table => {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });

            // 6. Registro de Service Worker para PWA
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker de Vacaciones registrado con éxito:', reg.scope))
                        .catch(err => console.error('Error al registrar Service Worker:', err));
                });
            }
        });
    </script>

</body>

</html>