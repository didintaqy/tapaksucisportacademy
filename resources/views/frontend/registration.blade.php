@extends('layout.master')

@section('title', 'Pendaftaran Siswa')

@section('hero')
    @include('layout.herosection.page-header', ['page' => 'Pendaftaran Siswa', 'breadcrumb' => 'Pendaftaran'])
@endsection

@section('content')
    <!-- Page Book Appointment Start -->
    <div class="page-book-appointment">
        <div class="container">
            <div class="row">
                <div class="col-xl-7 order-xl-1 order-2">
                    <!-- Registration Form Start -->
                    <div class="appointment-form">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Formulir Pendaftaran</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Daftarkan putra/putri Anda</h2>
                        </div>

                        <form action="{{ route('registration.store') }}" method="POST" class="wow fadeInUp">
                            @csrf
                            <div class="position-absolute" style="left: -9999px;" aria-hidden="true">
                                <label for="website">Website</label>
                                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 mb-4">
                                    <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" id="full_name"
                                        class="form-control @error('full_name') is-invalid @enderror"
                                        placeholder="Nama lengkap calon siswa" required value="{{ old('full_name') }}">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="nickname" class="form-label">Nama Panggilan</label>
                                    <input type="text" name="nickname" id="nickname"
                                        class="form-control @error('nickname') is-invalid @enderror"
                                        placeholder="Nama panggilan" value="{{ old('nickname') }}">
                                    @error('nickname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-control form-select @error('gender') is-invalid @enderror" required>
                                        <option value="" disabled @selected(! old('gender'))>Pilih jenis kelamin</option>
                                        <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                                        <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" id="birth_date"
                                        class="form-control @error('birth_date') is-invalid @enderror"
                                        required value="{{ old('birth_date') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="birth_place" class="form-label">Tempat Lahir</label>
                                    <input type="text" name="birth_place" id="birth_place"
                                        class="form-control @error('birth_place') is-invalid @enderror"
                                        placeholder="Kota kelahiran" value="{{ old('birth_place') }}">
                                    @error('birth_place')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="education" class="form-label">Pendidikan</label>
                                    <input type="text" name="education" id="education"
                                        class="form-control @error('education') is-invalid @enderror"
                                        placeholder="Contoh: SD, SMP, SMA" value="{{ old('education') }}">
                                    @error('education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="parent_name" class="form-label">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                                    <input type="text" name="parent_name" id="parent_name"
                                        class="form-control @error('parent_name') is-invalid @enderror"
                                        placeholder="Nama orang tua / wali" required value="{{ old('parent_name') }}">
                                    @error('parent_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="phone" class="form-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="08xxxxxxxxxx" required value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="email@contoh.com" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="program_id" class="form-label">Program yang Diinginkan</label>
                                    <select name="program_id" id="program_id"
                                        class="form-control form-select @error('program_id') is-invalid @enderror">
                                        <option value="" @selected(! old('program_id', request('program')))>Pilih program (opsional)</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->id }}"
                                                @selected((string) old('program_id', request('program')) === (string) $program->id)>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <label for="branch" class="form-label">Cabang yang Diinginkan</label>
                                    <input type="text" name="branch" id="branch"
                                        class="form-control @error('branch') is-invalid @enderror"
                                        placeholder="Contoh: Cabang Pusat" value="{{ old('branch') }}">
                                    @error('branch')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-12 mb-4">
                                    <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address" rows="3"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Alamat lengkap" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-12 mb-5">
                                    <label for="notes" class="form-label">Catatan Tambahan</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Riwayat latihan, kebutuhan khusus, atau pertanyaan">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <div class="appointment-form-btn">
                                        <button type="submit" class="btn-default"><span>Kirim Pendaftaran</span></button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if (session('success'))
                            <div class="alert alert-success mt-4" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    <!-- Registration Form End -->
                </div>

                <div class="col-xl-5 order-xl-2 order-1">
                    <!-- Registration Info Start -->
                    <div class="our-appointment-content">
                        @php
                            $contact = $site['contact'];
                            $requirements = collect(preg_split('/\r\n|\r|\n/', (string) ($site['registration']['requirements'] ?? '')))
                                ->map(fn ($line) => trim($line))
                                ->filter()
                                ->values();
                        @endphp

                        <div class="section-title">
                            <h3 class="wow fadeInUp">Bergabung Bersama Kami</h3>
                            <h2 class="text-anime-style-3" data-cursor="-opaque">Mulai perjalanan bela diri putra/putri Anda</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">
                                Isi formulir di samping. Admin akan menghubungi Anda untuk verifikasi data,
                                informasi biaya, dan jadwal latihan percobaan.
                            </p>
                        </div>

                        @if (filled($site['registration']['fee_label'] ?? null) || filled($site['registration']['fee_note'] ?? null) || $requirements->isNotEmpty())
                            <div class="p-4 rounded-3 bg-light mb-4 wow fadeInUp" data-wow-delay="0.3s">
                                <h3 class="h5 mb-2">Biaya &amp; Persyaratan</h3>
                                @if (filled($site['registration']['fee_label'] ?? null))
                                    <p class="mb-2">
                                        Biaya pendaftaran: <strong>{{ $site['registration']['fee_label'] }}</strong>
                                    </p>
                                @endif
                                @if (filled($site['registration']['fee_note'] ?? null))
                                    <p class="mb-3 text-muted">{{ $site['registration']['fee_note'] }}</p>
                                @endif
                                @if ($requirements->isNotEmpty())
                                    <ul class="mb-0 ps-3">
                                        @foreach ($requirements as $requirement)
                                            <li>{{ $requirement }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif

                        @if (filled($contact['phone'] ?? null) || filled($site['whatsapp_url'] ?? null) || filled($contact['email'] ?? null) || filled($contact['address'] ?? null))
                            <div class="appointment-info-item-list wow fadeInUp" data-wow-delay="0.4s">
                                @if (filled($contact['phone'] ?? null))
                                    <div class="appointment-info-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-phone-white.svg') }}" alt="">
                                        </div>
                                        <div class="appointment-info-item-content">
                                            <h3>Telepon</h3>
                                            <p><a href="tel:{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a></p>
                                        </div>
                                    </div>
                                @endif

                                @if (filled($site['whatsapp_url'] ?? null))
                                    <div class="appointment-info-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-phone-white.svg') }}" alt="">
                                        </div>
                                        <div class="appointment-info-item-content">
                                            <h3>WhatsApp</h3>
                                            <p>
                                                <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">
                                                    {{ $contact['whatsapp'] }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if (filled($contact['email'] ?? null))
                                    <div class="appointment-info-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-mail-white.svg') }}" alt="">
                                        </div>
                                        <div class="appointment-info-item-content">
                                            <h3>Email</h3>
                                            <p><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></p>
                                        </div>
                                    </div>
                                @endif

                                @if (filled($contact['address'] ?? null))
                                    <div class="appointment-info-item">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-location-white.svg') }}" alt="">
                                        </div>
                                        <div class="appointment-info-item-content">
                                            <h3>Lokasi Latihan</h3>
                                            <p>{{ $contact['address'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('schedules') }}" class="btn-default btn-highlighted">Lihat Jadwal Latihan</a>
                        </div>
                    </div>
                    <!-- Registration Info End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Book Appointment End -->
@endsection
