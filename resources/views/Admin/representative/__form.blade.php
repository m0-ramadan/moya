@if (session('success'))
    <div class="col-md-12 m-2">
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    </div>
@endif

<div class="row">
    <!-- Name -->

    <input name="id" id="name" type="hidden"
        value="{{ old('id', isset($representative) ? $representative->id : '') }}">



    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">الأسم</label>
        <input class="form-control @error('name') is-invalid @enderror" name="name" id="name" type="text"
            placeholder="الأسم" value="{{ old('name', $representative->name ?? '') }}" data-parsley-required="true"
            data-parsley-trigger="change">
        @error('name')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>


    <!-- Email -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="email" style="font-family: 'Cairo', sans-serif;">الأيميل</label>
        <input class="form-control @error('email') is-invalid @enderror" name="email" id="email" type="email"
            placeholder="الايميل" value="{{ old('email', $representative->email ?? '') }}" data-parsley-required="true"
            data-parsley-type="email" data-parsley-trigger="change">
        @error('email')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Phone -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="phone" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
        <input class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" type="text"
            placeholder="رقم الهاتف" value="{{ old('phone', $representative->phone ?? '') }}"
            data-parsley-required="true" data-parsley-trigger="change">
        @error('phone')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Phone 2 -->


    <!-- City (Region) -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="city" style="font-family: 'Cairo', sans-serif;">المدينة</label>
        <select class="form-control @error('city') is-invalid @enderror" name="city" id="city"
            data-parsley-required="true" data-parsley-trigger="change">
            <option value="">-- حدد المدينة --</option>
            @foreach ($regions as $region)
                <option value="{{ $region->id }}" @if (old('city', $representative->city ?? '') == $region->id) selected @endif>
                    {{ $region->region_ar }}
                </option>
            @endforeach
        </select>
        @error('city')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Address -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="address" style="font-family: 'Cairo', sans-serif;">العنوان</label>
        <input class="form-control @error('address') is-invalid @enderror" name="address" id="address" type="text"
            placeholder="Address" value="{{ old('address', $representative->address ?? '') }}">
        @error('address')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Gender -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="gender" style="font-family: 'Cairo', sans-serif;">نوع المندوب</label>
        <select class="form-control @error('gender') is-invalid @enderror" name="gander" id="gender">
            <option value="">-- حدد نوع المندوب --</option>
            <option value="1" @if (old('gender', $representative->gender ?? '') == '1') selected @endif>ذكر
            </option>
            <option value="2" @if (old('gender', $representative->gender ?? '') == '2') selected @endif>أنثى
            </option>

        </select>
        @error('gender')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Birth Date -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="birth_date" style="font-family: 'Cairo', sans-serif;"> تاريخ الميلاد</label>
        <input class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" id="birth_date"
            type="date" value="{{ old('birth_date', $representative->birth_date?->format('Y-m-d') ?? '') }}">
        @error('birth_date')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Password -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="password" style="font-family: 'Cairo', sans-serif;">كلمة المرور</label>
        <input class="form-control @error('password') is-invalid @enderror" name="password" id="password"
            type="password" placeholder="كلمة المرور" data-parsley-minlength="8" data-parsley-trigger="change"
            {{ $representative->exists ? '' : 'data-parsley-required=true' }}>
        @error('password')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Password Confirmation -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="password_confirmation" style="font-family: 'Cairo', sans-serif;">تأكيد كلمة
            المرور</label>
        <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
            id="password_confirmation" type="password" placeholder="Confirm Password"
            data-parsley-equalto="#password" data-parsley-trigger="change"
            {{ $representative->exists ? '' : 'data-parsley-required=true' }}>
        @error('password_confirmation')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Card Number -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="card_number" style="font-family: 'Cairo', sans-serif;">رقم الهوية</label>
        <input class="form-control @error('card_number') is-invalid @enderror" name="card_number" id="card_number"
            type="text" placeholder="رقم الهوية"
            value="{{ old('card_number', $representative->card_number ?? '') }}">
        @error('card_number')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Vehicle ID -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="vehicle_id" style="font-family: 'Cairo', sans-serif;"> رقم المركبة</label>
        <input class="form-control @error('vehicle_id') is-invalid @enderror" name="vehicle_id" id="vehicle_id"
            type="text" placeholder="رقم   المركبة"
            value="{{ old('vehicle_id', $representative->vehicle_id ?? '') }}">
        @error('vehicle_id')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Personal License Number -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="personal_license_number" style="font-family: 'Cairo', sans-serif;"> الرخصة
            الشخصية</label>
        <input class="form-control @error('personal_license_number') is-invalid @enderror"
            name="personal_license_number" id="personal_license_number" type="text" placeholder="الرخصة الشخصية"
            value="{{ old('personal_license_number', $representative->personal_license_number ?? '') }}">
        @error('personal_license_number')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Vehicle License Number -->
    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="vehicle_license_number" style="font-family: 'Cairo', sans-serif;">رقم رخصة
            السيارة</label>
        <input class="form-control @error('vehicle_license_number') is-invalid @enderror"
            name="vehicle_license_number" id="vehicle_license_number" type="text"
            placeholder="Vehicle License Number"
            value="{{ old('vehicle_license_number', $representative->vehicle_license_number ?? '') }}">
        @error('vehicle_license_number')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Status -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="status" style="font-family: 'Cairo', sans-serif;">حالة المندوب</label>
        <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
            <option value="1" @if (old('status', $representative->status ?? 1) == 1) selected @endif>مفعل</option>
            <option value="0" @if (old('status', $representative->status ?? 1) == 0) selected @endif>غير مفعل</option>
        </select>
        @error('status')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- type -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="type" style="font-family: 'Cairo', sans-serif;">نوع</label>

        @if (request()->routeIs('admin.representatives.createDrive'))
            {{-- سائق --}}
            <input type="text" class="form-control" value="سائق" readonly>
            <input type="hidden" name="type" value="0">
        @else
            {{-- مندوب --}}
            <input type="text" class="form-control" value="مندوب" readonly>
            <input type="hidden" name="type" value="1">
        @endif

        @error('type')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    @if (!request()->routeIs('admin.representatives.createDrive'))
        <!-- commission -->
        <div class="col-md-3" id="commission-container">
            <label class="mr-sm-2" for="commission" style="font-family: 'Cairo', sans-serif;">نسبة المندوب</label>
            <input class="form-control @error('commission') is-invalid @enderror" name="commission" id="commission"
                type="number" placeholder="commission"
                value="{{ old('commission', $representative->commission ?? '') }}">
            @error('commission')
                <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    @endif


    <!-- JavaScript to toggle commission field -->
    <script>
        function toggleCommissionField() {
            const typeSelect = document.getElementById('type');
            const commissionContainer = document.getElementById('commission-container');

            if (typeSelect.value === "0") {
                commissionContainer.style.display = "none";
            } else {
                commissionContainer.style.display = "block";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial check on page load
            toggleCommissionField();

            // Add event listener to select box
            document.getElementById('type').addEventListener('change', toggleCommissionField);
        });
    </script>




    <!-- Image Profile -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="image_profile" style="font-family: 'Cairo', sans-serif;">صورة الشخصية</label>
        <input class="form-control @error('image_profile') is-invalid @enderror" name="image_profile"
            id="image_profile" type="file" accept="image/*">
        @if ($representative->image_profile)
            <img src="{{ asset('public' . $representative->image_profile) }}" alt="Profile Image" width="100"
                class="mt-3">
        @endif
        @error('image_profile')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Passport Photo -->


    <!-- Card Photo -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="card_photo" style="font-family: 'Cairo', sans-serif;">صورة الهوية</label>
        <input class="form-control @error('card_photo') is-invalid @enderror" name="card_photo" id="card_photo"
            type="file" accept="image/*">
        @if ($representative->card_photo)
            <img src="{{ asset('public' . $representative->card_photo) }}" alt="Card Photo" width="100"
                class="mt-3">
        @endif
        @error('card_photo')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Vehicle Photo -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="vehicle_photo" style="font-family: 'Cairo', sans-serif;">صورة السيارة</label>
        <input class="form-control @error('vehicle_photo') is-invalid @enderror" name="vehicle_photo"
            id="vehicle_photo" type="file" accept="image/*">
        @if ($representative->vehicle_photo)
            <img src="{{ asset('public' . $representative->vehicle_photo) }}" alt="Vehicle Photo" width="100"
                class="mt-3">
        @endif
        @error('vehicle_photo')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Personal License Photo -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="personal_license_photo" style="font-family: 'Cairo', sans-serif;">صورة رخصة
            القيادة</label>
        <input class="form-control @error('personal_license_photo') is-invalid @enderror"
            name="personal_license_photo" id="personal_license_photo" type="file" accept="image/*">
        @if ($representative->personal_license_photo)
            <img src="{{ asset('public' . $representative->personal_license_photo) }}" alt="Personal License Photo"
                width="100" class="mt-3">
        @endif
        @error('personal_license_photo')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>

    <!-- Vehicle License Photo -->
    <div class="col-md-3">
        <label class="mr-sm-2" for="vehicle_license_photo" style="font-family: 'Cairo', sans-serif;">صورة رخصة
            المركبة</label>
        <input class="form-control @error('vehicle_license_photo') is-invalid @enderror" name="vehicle_license_photo"
            id="vehicle_license_photo" type="file" accept="image/*">
        @if ($representative->vehicle_license_photo)
            <img src="{{ asset('public' . $representative->vehicle_license_photo) }}" alt="Vehicle License Photo"
                width="100" class="mt-3">
        @endif
        @error('vehicle_license_photo')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>
</div>
