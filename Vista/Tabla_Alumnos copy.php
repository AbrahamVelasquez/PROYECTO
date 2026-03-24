<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto</title>
</head>
<body>
    <div class="flex justify-between items-center mb-8">
  <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white text-sm">👥</span>
    Listado de Alumnado
  </h2>
  <div class="flex gap-2">
    <button class="bg-slate-50 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-2 cursor-pointer">
      📥 Cargar Alumnos
    </button>
    <button class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
      + Agregar Alumno
    </button>
  </div>
</div>

<div class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
  <div class="flex-1 relative">
    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
    <input type="text" placeholder="BUSCAR POR APELLIDOS O DNI..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
  </div>
  <div class="flex items-center gap-3">
    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
    <select class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
      <option value="">TODOS LOS ESTADOS</option>
      <option value="sin-asignar">🔴 SIN ASIGNAR</option>
      <option value="en-proceso">🟡 EN PROCESO</option>
      <option value="completado">🟢 COMPLETADO</option>
    </select>
  </div>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
  <table class="w-full text-left table-tech border-collapse">
    <thead>
      <tr>
        <th>APELLIDOS, NOMBRE ALUMNO</th>
        <th class="w-10">SEXO</th>
        <th class="w-24 border-section">DNI / NIE</th>
        <th>NOMBRE EMPRESA</th>
        <th class="w-16">Nº CONV.</th>
        <th class="border-section">DIRECCIÓN CENTRO TRABAJO</th>
        <th class="w-20">F. INICIO</th>
        <th class="w-20">F. FINAL</th>
        <th class="w-24">HORARIO</th>
        <th class="w-12 border-section">H/DÍA</th>
        <th class="w-24">ESTADO</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 uppercase bg-white">
      
      <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="font-bold">MARTÍNEZ SOSA, CARLOS</td>
        <td class="text-center">H</td>
        <td class="text-center font-mono border-section">44556677Z</td>
        <td colspan="7" class="text-center bg-red-50/30 text-red-600 border-section tracking-[0.2em] font-black italic py-4">
          ⚠️ PENDIENTE DE ASIGNACIÓN DE PLAZA
        </td>
        <td class="text-center">
          <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[8px] border border-red-200 font-black">SIN ASIGNAR</span>
        </td>
      </tr>

      <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="font-bold">GARCÍA MARTÍNEZ, ALEJANDRO</td>
        <td class="text-center">H</td>
        <td class="text-center font-mono border-section">12345678X</td>
        <td class="text-orange-600">INDITEX S.A.</td>
        <td class="text-center">0892</td>
        <td colspan="5" class="text-center bg-orange-50/30 text-orange-600 border-section tracking-wider font-black italic py-4">
          ⚠️ FALTA DIRECCIÓN, FECHAS Y HORARIO
        </td>
        <td class="text-center">
          <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[8px] border border-amber-200 font-black">EN PROCESO</span>
        </td>
      </tr>

      <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="font-bold">REYES CALVO, LUCÍA</td>
        <td class="text-center">M</td>
        <td class="text-center font-mono border-section">55661122K</td>
        <td>PIXEL ART STUDIO</td>
        <td class="text-center">0125</td>
        <td class="border-section text-[9px] lowercase font-medium">Av. Libertad 42, Sevilla</td>
        <td class="text-center">01/03/26</td>
        <td class="text-center">20/06/26</td>
        <td colspan="2" class="text-center bg-orange-50/30 text-orange-600 border-section font-black italic">
          ⚠️ FALTA HORARIO
        </td>
        <td class="text-center">
          <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[8px] border border-amber-200 font-black">EN PROCESO</span>
        </td>
      </tr>

      <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="font-bold">LOPEZ RUIZ, MARIA ESTHER</td>
        <td class="text-center">M</td>
        <td class="text-center font-mono border-section">77123321Y</td>
        <td>TECH SOLUTIONS S.L.</td>
        <td class="text-center">0042</td>
        <td class="border-section text-[9px] lowercase font-medium">C/ Mayor 15, Madrid</td>
        <td class="text-center">01/03/26</td>
        <td class="text-center">20/06/26</td>
        <td class="text-center">08:00 - 14:00</td>
        <td class="text-center border-section">6</td>
        <td class="text-center">
          <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[8px] border border-emerald-200 font-black">COMPLETADO</span>
        </td>
      </tr>

      <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="font-bold">DOMÍNGUEZ FERRI, MARC</td>
        <td class="text-center">H</td>
        <td class="text-center font-mono border-section">33998844M</td>
        <td>CLOUD NETWORKS</td>
        <td class="text-center">0551</td>
        <td class="border-section text-[9px] lowercase font-medium">P. de Gracia 10, Barcelona</td>
        <td class="text-center">15/03/26</td>
        <td class="text-center">30/06/26</td>
        <td class="text-center">09:00 - 15:00</td>
        <td class="text-center border-section">6</td>
        <td class="text-center">
          <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[8px] border border-emerald-200 font-black">COMPLETADO</span>
        </td>
      </tr>

    </tbody>
  </table>
</div>

<div class="mt-6 flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
  <p>Mostrando 5 alumnos asignados a tu tutoría</p>
  <div class="flex gap-2">
    <button class="px-3 py-1 border border-slate-200 rounded hover:bg-white transition-colors cursor-not-allowed">Ant.</button>
    <button class="px-3 py-1 border border-slate-200 rounded hover:bg-white transition-colors cursor-not-allowed">Sig.</button>
  </div>
</div>
</body>
</html>