<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestión FFE — Instituto FP</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    [data-tab] { display: none; }
    [data-tab].active { display: block; }
    
    .step-label { user-select: none; cursor: pointer; }
    .step-active-circle {
      background-color: #ea580c !important;
      color: white !important;
      box-shadow: 0 0 0 4px white, 0 0 0 7px #fed7aa;
    }
    .step-active-text { color: #111827 !important; font-weight: 800 !important; }

    /* TOOLTIP */
    .help-trigger { position: relative; display: inline-flex; }
    .tooltip-box {
      visibility: hidden; opacity: 0; position: absolute; bottom: 150%; left: 50%; transform: translateX(-50%) scale(0.95);
      width: 180px; background-color: #1e293b; color: #fff; text-align: center; padding: 10px; border-radius: 8px;
      font-size: 10px; text-transform: none; z-index: 100; transition: all 0.2s ease;
    }
    .help-trigger:hover .tooltip-box { visibility: visible; opacity: 1; transform: translateX(-50%) scale(1); }
  </style>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans">
  <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      
      <div class="pt-6 px-8 pb-0"> 
        <div class="flex justify-between items-start mb-6">
          <div class="flex-1 min-w-0"> 
            <h1 class="text-4xl font-extrabold tracking-tight">
              Gestión de <span class="text-orange-600">Prácticas FFE</span>
            </h1>
            <p class="mt-1 text-slate-500 text-sm max-w-2xl truncate">
              Interfaz de gestión interna FFE. Selecciona una fase para gestionar el registro, asignación o seguimiento.
            </p>
          </div>

          <div class="relative ml-4 flex-shrink-0">
            <button id="userMenuBtn" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2 pr-4 hover:bg-slate-50 transition-all shadow-sm">
              <div class="h-8 w-8 rounded-lg bg-orange-600 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                <?= strtoupper(substr($nombreTutor, 0, 1)) ?>
              </div>
              <span class="text-xs font-bold text-slate-700"><?= $nombreTutor ?></span>
              <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
              <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tutoría Asignada</p>
                <p class="text-[11px] font-bold text-slate-800 uppercase italic">
                  <?php 
                    $cursoLimpio = mb_strtolower(trim($cursoTutor));
                    $abreviatura = (str_contains($cursoLimpio, 'primero')) ? "1º" : ((str_contains($cursoLimpio, 'segundo')) ? "2º" : $cursoTutor);
                    echo $abreviatura . " " . $cicloTutor; 
                  ?>
                </p>
              </div>
              <div class="p-1">
                <form action="index.php" method="POST">
                  <button type="submit" name="btnLogOut" class="w-full text-left px-4 py-3 text-[10px] font-black text-red-500 hover:bg-red-50 rounded-lg transition-colors uppercase flex items-center justify-between">
                    <span>Cerrar Sesión</span>
                    <span>✕</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative mb-6">
          <div class="hidden md:block absolute top-2.5 left-0 w-full h-0.5 bg-slate-100 -z-0"></div>
          <?php 
          $pasos = [
              1 => ['tit' => 'Convenios', 'desc' => 'Gestión de acuerdos legales con empresas.'],
              2 => ['tit' => 'Alumnos', 'desc' => 'Base de datos y asignación a plazas.'],
              3 => ['tit' => 'Plan Formativo', 'desc' => 'Definición de tareas y competencias.'],
              4 => ['tit' => 'Seguimiento', 'desc' => 'Valoración y cierre de expediente.']
          ];
          foreach($pasos as $num => $info): ?>
          <div class="step-label relative z-10 flex flex-col items-center text-center" data-step="<?= $num ?>">
            <div class="step-circle flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 transition-all duration-300 mb-2 border-2 border-white shadow-sm">
              <span class="text-[12px] font-bold"><?= $num ?></span>
            </div>
            <div class="flex items-center gap-2">
              <h3 class="step-heading text-slate-400 text-sm transition-all duration-300 uppercase tracking-wider"><?= $info['tit'] ?></h3>
              <div class="help-trigger">
                <span class="cursor-help flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 text-[10px] text-slate-400 font-bold hover:bg-slate-100">?</span>
                <div class="tooltip-box"><?= $info['desc'] ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="border-t border-slate-100 p-10 bg-white min-h-[500px]">
        
        <div data-tab="1" class="<?= $pestanaActiva == 1 ? 'active' : '' ?>">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold flex items-center gap-3">🏢 Gestión de Convenios</h2>
            <a href="Vista/Registro_Convenio.php" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-900 transition-all shadow-lg">
              + Registrar Nuevo Convenio
            </a>
          </div>
          
          <form action="index.php" method="GET" class="flex gap-3 w-full mb-10">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" 
              placeholder="CIF O NOMBRE DE EMPRESA..." 
              class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-6 py-4 outline-none focus:ring-4 focus:ring-orange-50 text-xs font-bold uppercase transition-all">
            <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">Buscar</button>
          </form>

          <?php if (!empty($_GET['busqueda'])): ?>
            <div class="mb-10">
              <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-center">Resultados</h3>
              <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                  <thead class="bg-slate-900 text-white">
                    <tr>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Nº</th>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Empresa / CIF</th>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Municipio</th>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Contacto</th>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Representante</th>
                      <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($convenios)): foreach ($convenios as $c): ?>
                      <tr class="hover:bg-slate-50 transition-colors text-sm">
                        <td class="px-4 py-5 text-center font-mono text-slate-400 font-bold">#<?= $c['id_convenio'] ?></td>
                        <td class="px-4 py-5">
                          <div class="font-bold uppercase italic text-slate-900"><?= $c['nombre_empresa'] ?></div>
                          <div class="text-xs text-slate-400 font-mono"><?= $c['cif'] ?></div>
                        </td>
                        <td class="px-4 py-5 font-bold text-slate-600 uppercase"><?= $c['municipio'] ?></td>
                        <td class="px-4 py-5">
                          <div class="font-bold text-slate-700"><?= $c['telefono'] ?></div>
                          <div class="text-xs text-orange-600 font-medium"><?= $c['mail'] ?></div>
                        </td>
                        <td class="px-4 py-5 font-bold text-slate-500 uppercase"><?= $c['nombre_representante'] ?></td>
                        <td class="px-4 py-5 text-center">
                          <form action="index.php?busqueda=<?= urlencode($_GET['busqueda']) ?>" method="POST">
                            <input type="hidden" name="id_convenio_fav" value="<?= $c['id_convenio'] ?>">
                            <button type="submit" name="btnFavorito" class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all">⭐ Añadir</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="6" class="px-6 py-16 text-center text-red-500 text-sm font-black uppercase italic">⚠ Sin resultados para "<?= htmlspecialchars($_GET['busqueda']) ?>".</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

          <div class="mt-12">
            <h3 class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-4">Mi Listado Personal</h3>
            <div class="overflow-hidden rounded-2xl border-2 border-orange-100 bg-white">
              <table class="w-full text-left border-collapse">
                <thead class="bg-orange-500 text-white text-[10px] font-black uppercase tracking-widest">
                  <tr>
                    <th class="px-6 py-3">Empresa Seleccionada</th>
                    <th class="px-6 py-3 text-center">Gestión</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                  <?php if (!empty($misConvenios)): foreach ($misConvenios as $mc): ?>
                    <tr class="hover:bg-orange-50/50 transition-colors">
                      <td class="px-6 py-5">
                        <div class="font-bold text-slate-900 uppercase text-sm"><?= $mc['nombre_empresa'] ?></div>
                        <div class="text-xs text-slate-400 font-bold"><?= $mc['municipio'] ?></div>
                      </td>
                      <td class="px-6 py-5">
                        <form action="index.php<?= !empty($_GET['busqueda']) ? '?busqueda='.urlencode($_GET['busqueda']) : '' ?>" method="POST" onsubmit="return confirm('¿Quitar de la lista?');">
                          <input type="hidden" name="id_convenio_eliminar" value="<?= $mc['id_convenio'] ?>">
                          <button type="submit" name="btnEliminarFav" class="group flex items-center gap-2 mx-auto bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-4 py-2 rounded-lg transition-all border border-red-100">
                            <span class="text-[10px] font-black uppercase">Eliminar</span>
                            <span class="text-xs group-hover:rotate-90 transition-transform">✕</span>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                    <tr><td colspan="2" class="px-6 py-12 text-center text-slate-300 text-xs font-black uppercase italic">Tu listado está vacío</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div data-tab="2" class="<?= $pestanaActiva == 2 ? 'active' : '' ?>">
          <?php include 'Vista/Tabla_Alumnos.php'; ?>
        </div>

        <div data-tab="3" class="text-center text-slate-400 italic py-20 uppercase font-black text-[10px] tracking-widest">Módulo de Plan Formativo en desarrollo.</div>
        <div data-tab="4" class="text-center text-slate-400 italic py-20 uppercase font-black text-[10px] tracking-widest">Módulo de Seguimiento en desarrollo.</div>

      </div>
    </div> 
  </main>
  <footer class="mt-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
    © <?= date('Y') ?> — Gestión FFE interna.
  </footer>
  <script src="Public/js/script_tabs.js"></script>
</body>
</html>