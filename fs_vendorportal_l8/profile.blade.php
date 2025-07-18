@extends('layouts.app')
@section('title', 'Vendor Profile')
@section('content')
<style>
body { background: #f8f9fb; margin: 0; font-family: 'Inter', 'Segoe UI', sans-serif; }
.header { background: linear-gradient(135deg, #4ecf6a, #31bfa4); color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 12px 30px; margin: 20px 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.header .left-section { display: flex; align-items: center; gap: 12px; }
.almulla-logo { width: 45px; height: 45px; }
.header h1 { font-size: 1.4rem; font-weight: 600; }
.right-section h2 { font-size: 1.2rem; font-weight: 500; }
.container-grid { display: grid; grid-template-columns: 1fr 1fr; grid-template-areas: "vendor contact" "vendor address" "buttons buttons"; gap: 20px; margin: auto; padding: 20px; max-width: 1100px; }
.vendor { grid-area: vendor; }
.contact { grid-area: contact; }
.address { grid-area: address; }
.buttons { grid-area: buttons; display: flex; justify-content: center; gap: 20px; }
.box { background: white; border: 1px solid #e1e4e8; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.06); padding: 25px 15px; position: relative; }
.section-header { position: absolute; top: -14px; left: 16px; background: #1fae4b; color: white; padding: 4px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.line { border-top: 1px solid #ddd; margin-bottom: 10px; }
.details-grid, .form-grid { display: grid; grid-template-columns: auto 1fr; gap: 12px 10px; align-items: center; }
input, textarea, select { padding: 6px 8px; border: 1px solid #ccc; border-radius: 5px; background: #f9fafc; transition: border 0.2s; }
input:focus, textarea:focus, select:focus { border: 1px solid #1fae4b; outline: none; }
textarea[name="vendor_name"] { font-size: 1.2rem; font-weight: 600; }
button.btn-edit, button.btn-save, button.btn-change { background: white; border: 1.5px solid #1fae4b; color: #1fae4b; border-radius: 6px; padding: 8px 20px; font-weight: 700; cursor: pointer; transition: background 0.3s, color 0.3s; }
button.btn-edit:hover, button.btn-save:hover, button.btn-change:hover { background: #1fae4b; color: white; }
.shake { animation: shake 0.3s; border: 2px solid red; }
@keyframes shake { 0% { transform: translateX(0); } 25% { transform: translateX(-4px); } 50% { transform: translateX(4px); } 75% { transform: translateX(-4px); } 100% { transform: translateX(0); } }
.password-popup-theme { border-radius: 10px !important; box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important; border: 1px solid #4ecf6a !important; font-family: 'Inter', 'Segoe UI', sans-serif !important; padding: 10px !important; }
.btn-confirm-green { background-color: #4ecf6a !important; color: #ffffff !important; border-radius: 5px !important; font-weight: 600 !important; padding: 8px 18px !important; }
.btn-cancel-gray { background-color: #f1f5f9 !important; color: #374151 !important; border-radius: 5px !important; font-weight: 500 !important; padding: 8px 18px !important; }
.swal2-input.swal2-password-input { width: 260px !important; padding: 10px 14px !important; border: 1px solid #d1d5db !important; border-radius: 6px !important; background: #f9fafb !important; font-family: 'Inter', 'Segoe UI', sans-serif !important; font-size: 1rem !important; transition: border 0.3s ease; }
.swal2-input.swal2-password-input:focus { border-color: #4ecf6a !important; outline: none !important; box-shadow: 0 0 0 2px rgba(78, 207, 106, 0.2) !important; }
.custom-popup-confirm {width: 320px !important;padding: 16px 24px !important;border: 1.5px solid #4ecf6a !important;border-radius: 12px !important;box-shadow: 0 6px 18px rgba(0,0,0,0.1);font-family: 'Inter', sans-serif;font-size: 14px;}
.custom-popup-confirm .swal2-title {font-size: 1.1rem !important;font-weight: 600 !important;margin: 0 0 4px 0 !important;}
.custom-popup-confirm .swal2-html-container { margin: 0 !important;font-weight: 400;color: #555;}
.confirm-green-btn {background-color: #4ecf6a !important;color: #fff !important;padding: 8px 18px !important;border-radius: 6px !important;font-weight: 600 !important;font-size: 14px !important;border: none !important;margin: 0 6px;cursor: pointer;}
.cancel-outline-btn {background-color: #fff !important;color: #4ecf6a !important;border: 1.5px solid #4ecf6a !important;padding: 8px 18px !important;border-radius: 6px !important;font-weight: 600 !important;font-size: 14px !important;margin: 0 6px;cursor: pointer;}
</style>

<div class="header">
  <div class="left-section">
    <img src="{{ asset('almulla-logo-small.png') }}" class="almulla-logo" alt="Al Mulla Group Logo">
    <h1>Foreign Supplier Portal - AlMulla Group</h1>
  </div>
  <div class="right-section">
    <h2>Vendor Profile</h2>
  </div>
</div>

<form method="POST" action="{{ route('vendor.profile.save', ['vendor' => $vendor->LIFNR]) }}">
    @csrf
    <div class="container-grid">

        <!-- Vendor Info -->
        <div class="box vendor">
            <div class="section-header">Vendor Details</div>
            <div class="line"></div>
            <textarea name="NAME1" placeholder="Name of Vendor" style="width: 96%; margin-bottom:5px;">{{ $vendor->NAME1 }}</textarea>
            <div class="form-grid">
                <label>Vendor ID:</label>
                <input type="text" name="LIFNR" value="{{ $vendor->LIFNR }}" readonly>

                <label>Country:</label>
                <input type="text" name="LAND1" value="{{ $vendor->LAND1 }}" readonly>

                <label>Telephone:</label>
                <input type="text" name="TELF1" value="{{ $vendor->TELF1 }}">

                <label>Status:</label>
                <select name="status" required>
                    <option value="" disabled {{ $vendor->status ? '' : 'selected' }}>Select Status</option>
                    <option value="active" {{ $vendor->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="not active" {{ $vendor->status == 'not active' ? 'selected' : '' }}>Not Active</option>
                </select>
            </div>
        </div>

        <!-- Contact -->
        <div class="box contact">
            <div class="section-header">Contact</div>
            <div class="line"></div>
            <div class="form-grid">
                <label>Email:</label>
                <input type="email" name="EMAIL" value="{{ $vendor->EMAIL }}">

                <label>Telephone:</label>
                <input type="text" name="TELF1" value="{{ $vendor->TELF1 }}">
            </div>
        </div>

        <!-- Address -->
        <div class="box address">
            <div class="section-header">Address</div>
            <div class="line"></div>
            <div class="form-grid">
                <label>House Number:</label>
                <input type="text" name="HOUSE_NUM1" value="{{ $vendor->HOUSE_NUM1 }}">

                <label>Street 2:</label>
                <input type="text" name="STR_SUPPL1" value="{{ $vendor->STR_SUPPL1 }}">

                <label>Building:</label>
                <input type="text" name="BUILDING" value="{{ $vendor->BUILDING }}">

                <label>City:</label>
                <input type="text" name="CITY1" value="{{ $vendor->CITY1 }}">

                <label>Region:</label>
                <input type="text" name="REGION" value="{{ $vendor->REGION }}">

                <label>PO Box:</label>
                <input type="text" name="PFACH" value="{{ $vendor->PFACH }}">

                <label>Postal Code:</label>
                <input type="text" name="PSTL2" value="{{ $vendor->PSTL2 }}">
            </div>
        </div>

        <!-- Buttons -->
        <div class="buttons">
            <button class="btn-change" type="button">CHANGE PASSWORD</button>
            <button class="btn-edit" type="button">EDIT</button>
            <button class="btn-save" type="submit">SAVE</button>
        </div>
    </div>
</form>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const editButton = document.querySelector('.btn-edit');
    const saveButton = document.querySelector('.btn-save');
    const form = document.querySelector('form');
    const allInputs = form.querySelectorAll('input, textarea, select');

    allInputs.forEach(input => {
        input.readOnly = true;
        if (input.tagName === 'SELECT') input.disabled = true;
    });

    editButton.addEventListener('click', () => {
        allInputs.forEach(input => {
            if (
                input.name === "vendor_name" ||
                input.name === "status" ||
                input.name === "email" ||
                input.name === "phone" ||
                input.name === "mobile" ||
                input.name === "address_line1" ||
                input.name === "address_line2" ||
                input.name === "city" ||
                input.name === "country_address" ||
                input.name === "postal_code" ||
                input.name === "po_box"
            ) {
                input.readOnly = false;
                if (input.tagName === 'SELECT') input.disabled = false;
                input.style.border = "1px solid black";
            }
        });
    });



   saveButton.addEventListener('click', (e) => {
    e.preventDefault();

    let hasErrors = false;
    allInputs.forEach(field => {
        if (!field.readOnly && field.value.trim() === '') {
            hasErrors = true;
            field.classList.add('shake');
            field.style.border = '2px solid red';
            setTimeout(() => {
                field.classList.remove('shake');
                field.style.border = '';
            }, 500);
        }
    });

    if (hasErrors) {
        showToast("Please fill all required fields!");
        return;
    }

    Swal.fire({
        title: 'Save Changes?',
        html: `<p style="font-size: 0.95rem; margin-top: -10px;">Confirm to save your updates</p>`,
        showCancelButton: true,
        confirmButtonText: 'Save',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        color: '#2d3748',
        customClass: {
            popup: 'custom-popup-confirm',
            confirmButton: 'confirm-green-btn',
            cancelButton: 'cancel-outline-btn'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});




    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.background = '#333';
        toast.style.color = '#fff';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '6px';
        toast.style.zIndex = 9999;
        toast.style.opacity = 0;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = 1; }, 50);
        setTimeout(() => {
            toast.style.opacity = 0;
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 2000);
    }
});
</script>

@if(session('success'))
<script>
Swal.fire({
    icon: "success",
    title: "Profile Saved Successfully",
    text: "{{ session('success') }}",
    confirmButtonColor: "#4ecf6a",
    background: "#ffffff",
    color: "#333333"
});
</script>
@endif


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const changePassButton = document.querySelector('.btn-change');
    if (changePassButton) {
        changePassButton.addEventListener("click", () => {
            Swal.fire({
                title: "<span style='color: #4ecf6a; font-family: Inter, Segoe UI, sans-serif;'>Change Password</span>",
                html: `
                    <input type="password" id="oldPassword" class="swal2-input swal2-password-input" placeholder="Old Password">
                    <input type="password" id="newPassword" class="swal2-input swal2-password-input" placeholder="New Password">
                    <input type="password" id="confirmPassword" class="swal2-input swal2-password-input" placeholder="Confirm New Password">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: "Change",
                cancelButtonText: "Cancel",
                background: "#ffffff",
                color: "#333333",
                customClass: {
                    popup: 'password-popup-theme',
                    confirmButton: 'btn-confirm-green',
                    cancelButton: 'btn-cancel-gray'
                },
                preConfirm: () => {
                    const oldPassword = Swal.getPopup().querySelector("#oldPassword").value;
                    const newPassword = Swal.getPopup().querySelector("#newPassword").value;
                    const confirmPassword = Swal.getPopup().querySelector("#confirmPassword").value;

                    if (!oldPassword || !newPassword || !confirmPassword) {
                        Swal.showValidationMessage("⚠️ All fields are required");
                        return false;
                    }
                    if (newPassword !== confirmPassword) {
                        Swal.showValidationMessage("🚫 New passwords do not match");
                        return false;
                    }
                    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z\\d]).{8,}$/;
                    if (!passwordRegex.test(newPassword)) {
                        Swal.showValidationMessage(
                            "Password must be at least 8 characters, with uppercase, lowercase, number, and symbol"
                        );
                        return false;
                    }
                    return { oldPassword, newPassword };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Are you sure you want to change your password?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Change",
                        cancelButtonText: "No",
                        background: "#ffffff",
                        color: "#333333",
                        confirmButtonColor: "#4ecf6a",
                        cancelButtonColor: "#e2e8f0",
                        customClass: {
                            confirmButton: 'btn-confirm-green',
                            cancelButton: 'btn-cancel-gray'
                        }
                    }).then(confirmResult => {
                        if (confirmResult.isConfirmed) {
                            fetch("{{ route('vendor.change.password') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    old_password: result.value.oldPassword,
                                    new_password: result.value.newPassword
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Password Changed",
                                        text: "Your password was updated successfully!",
                                        background: "#ffffff",
                                        color: "#333333",
                                        confirmButtonColor: "#4ecf6a"
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: data.message,
                                        background: "#ffffff",
                                        color: "#333333",
                                        confirmButtonColor: "#dc3545"
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Something went wrong, please try again.",
                                    background: "#ffffff",
                                    color: "#333333",
                                    confirmButtonColor: "#dc3545"
                                });
                            });
                        }
                    });
                }
            });
        });
    }
});
</script>
@endsection
