@php
$role = auth()->user()->role ?? 'admin';

$menuConfig = [
'admin' => [
[ 'name' => 'Beranda', 'icon' => 'home.png', 'path' => '/dashboard' ],
[ 'name' => 'Data Siswa', 'icon' => 'home.png', 'path' => '/siswa' ],
[ 'name' => 'Data Guru', 'icon' => 'home.png', 'path' => '/guru' ],
[ 'name' => 'Data Kelas', 'icon' => 'home.png', 'path' => '/kelas' ],
[ 'name' => 'Mata Pelajaran', 'icon' => 'home.png', 'path' => '/mapel' ],
[ 'name' => 'Kehadiran Siswa', 'icon' => 'home.png', 'path' => '/kehadiran' ],
[ 'name' => 'Rekap Kehadiran', 'icon' => 'home.png', 'path' => '/rekap' ],
[ 'name' => 'Manajemen Pengguna','icon' => 'home.png', 'path' => '/users' ],
[ 'name' => 'Pengaturan', 'icon' => 'home.png', 'path' => '/settings' ],
],
'guru' => [
[ 'name' => 'Beranda', 'icon' => 'home.png', 'path' => '/dashboard' ],
[ 'name' => 'Jadwal Mengajar','icon' => 'home.png', 'path' => '/jadwal' ],
[ 'name' => 'Absensi Siswa', 'icon' => 'home.png', 'path' => '/absensi' ],
[ 'name' => 'Rekap Absensi', 'icon' => 'home.png', 'path' => '/rekap' ],
[ 'name' => 'Pengaturan', 'icon' => 'home.png', 'path' => '/settings' ],
],
'siswa' => [
[ 'name' => 'Beranda', 'icon' => 'home.png', 'path' => '/dashboard' ],
[ 'name' => 'Absensi Siswa', 'icon' => 'home.png', 'path' => '/absensi' ],
[ 'name' => 'Laporan', 'icon' => 'home.png', 'path' => '/laporan' ],
[ 'name' => 'Pengaturan', 'icon' => 'home.png', 'path' => '/settings' ],
],
];

$menus = $menuConfig[$role] ?? [];

@endphp

<div class="sidebar p-3">

    <div class="sidebar-logo text-center fw-bold mb-3">
        Logo Sekolah
    </div>

    <div class="px-2 mt-2 text-muted fw-bold small">Menu</div>

    <ul class="list-unstyled mt-2">
        @foreach ($menus as $menu)
        <li>
            <a href="{{ url($menu['path']) }}"
                class="d-flex align-items-center gap-2 p-2 sidebar-row 
                   {{ request()->is(trim($menu['path'], '/').'*') ? 'active bg-primary text-white' : '' }}
                   ">

                <img src="{{ asset('assets/img/'.$menu['icon']) }}"
                    alt="{{ $menu['name'] }}"
                    width="22" height="22">

                <span>{{ $menu['name'] }}</span>
            </a>
        </li>
        @endforeach
        <li>
            <a href="{{ route('logout') }}"
                class="d-flex align-items-center gap-2 p-2 sidebar-row text-danger">
                <img src="{{ asset('assets/img/home.png') }}"
                    width="22" height="22">
                Keluar
            </a>
        </li>
    </ul>

</div>