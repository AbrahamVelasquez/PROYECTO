<?php
session_start();
$esExterno = !isset($_SESSION['usuario']);
$id_ciclo = $_GET['id_ciclo'] ?? '';

// Si es externo y NO hay ciclo, bloqueamos todo.
if ($esExterno && empty($id_ciclo)) {
    include 'Modales_Registro.php';
    echo "<script>document.getElementById('modalErrorCiclo').classList.remove('hidden');</script>";
    die(); 
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema FCT — Registro de Convenio</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans">
  <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    
    <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
      
      <div class="p-8 flex justify-between items-center bg-white border-b border-slate-100">
        <div>
          <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 uppercase">
            Registro de <span class="text-orange-600">Convenio</span>
          </h1>
          <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">
            Introduzca los datos oficiales de la empresa
          </p>
        </div>
        <?php if (isset($_SESSION['usuario'])): ?>
        <a href="../index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-xl text-[10px] font-black uppercase transition-all">
            ← Cancelar
        </a>
      <?php endif; ?>
      </div>
      
      <?php 
        $id_ciclo_actual = $_GET['id_ciclo'] ?? $_SESSION['id_ciclo'] ?? ''; 
      ?>

      <form id="formRegistro" action="<?= $esExterno ? 'Procesar.php' : '../index.php' ?>" method="POST" class="p-10 space-y-8">        
        <input type="hidden" name="accion" value="guardarNuevoConvenio">
        <input type="hidden" name="id_ciclo" value="<?= htmlspecialchars($id_ciclo_actual) ?>"> 

        <section class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-3">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Nombre de la empresa</label>
              <input type="text" name="nombre_empresa" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">CIF</label>
              <input type="text" name="cif" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-4">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Dirección</label>
              <input type="text" name="direccion" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-2">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Municipio</label>
              <input type="text" name="municipio" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">CP</label>
              <input type="text" name="cp" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">País</label>
              <input type="text" name="pais" value="ESPAÑA" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Tfno</label>
              <input type="text" name="telefono" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">FAX</label>
              <input type="text" name="fax" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-2">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Mail</label>
              <input type="email" name="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
          </div>
        </section>

        <section class="pt-6 border-t border-slate-100 space-y-6">
          <h2 class="text-xs font-black uppercase text-orange-600 tracking-tighter italic">Datos del Representante Legal</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Nombre y apellidos</label>
              <input type="text" name="nombre_rep_legal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">DNI</label>
              <input type="text" name="dni_rep_legal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
            <div class="md:col-span-1">
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Cargo</label>
              <input type="text" name="cargo_rep_legal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold uppercase outline-none focus:ring-2 focus:ring-orange-500/20">
            </div>
          </div>
        </section>

      <div class="pt-10 flex justify-end">
        <button onclick="abrirConfirmacion()" type="button" class="bg-slate-900 text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-2xl active:scale-95">
          Finalizar Registro de Convenio
        </button>
      </div>
      </form>
    </div>
  </main>

  <?php include 'Modales_Registro.php'; ?>

  <script>
    function abrirConfirmacion() {
        document.getElementById('modalConfirmar').classList.remove('hidden');
    }

    function cerrarConfirmacion() {
        document.getElementById('modalConfirmar').classList.add('hidden');
    }

    function ejecutarEnvioReal() {
      const form = document.getElementById('formRegistro');
      
      // Validación básica de HTML5 (required) antes de enviar
      if (!form.checkValidity()) {
          cerrarConfirmacion();
          form.reportValidity();
          return;
      }

      const formData = new FormData(form);

      <?php if ($esExterno): ?>
          cerrarConfirmacion();
          
          fetch('Procesar.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              // Si la respuesta es OK, mostramos el modal de éxito
              if (response.ok) {
                  document.getElementById('modalExito').classList.remove('hidden');
                  form.reset(); // Aquí se resetean los inputs como pediste
              } else {
                  alert("Error en el servidor al procesar el registro.");
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert("Error de conexión.");
          });
      <?php else: ?>
          // Si hay sesión, el formulario se envía al index.php normalmente
          form.submit();
      <?php endif; ?>
  }
</script>
</body>
</html>