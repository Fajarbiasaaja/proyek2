# 👤 Daftar Akun Test & Password JasaKu

## ⚠️ PENTING: Test Account Only
Akun-akun berikut adalah **untuk testing development saja**. 
Jangan gunakan di production dengan password ini!

---

## 🔴 ADMIN ACCOUNTS

### Admin 1 (Super Admin)
```
Email:     admin@jasaku.com
Password:  Admin@123456
Role:      admin
Status:    Active
```

### Admin 2 (Secondary Admin)
```
Email:     admin2@jasaku.com
Password:  SecurePass123!
Role:      admin
Status:    Active
```

---

## 🔵 CUSTOMER ACCOUNTS

### Customer 1 (Aktif)
```
Email:     customer1@example.com
Password:  Customer@123
Role:      customer
Status:    Active
Location:  Jakarta Selatan
Phone:     081234567890
```

### Customer 2 (Aktif)
```
Email:     budi@example.com
Password:  Budi@12345
Role:      customer
Status:    Active
Location:  Jakarta Pusat
Phone:     082345678901
```

### Customer 3 (Aktif)
```
Email:     siti@example.com
Password:  Siti@12345
Role:      customer
Status:    Active
Location:  Tangerang
Phone:     085678901234
```

### Customer 4 (Test)
```
Email:     test.customer@example.com
Password:  TestPass@123
Role:      customer
Status:    Active
Location:  Bekasi
Phone:     089876543210
```

---

## 🟠 TECHNICIAN ACCOUNTS

### Technician 1 (Expert AC Repair)
```
Email:     ahmad.teknisi@provider.com
Password:  TechPass@123
Role:      technician
Status:    Active
Name:      Ahmad Teknisi
Service:   AC Repair & Maintenance
Rating:    4.8/5
Experience: 15 tahun
```

### Technician 2 (General Service)
```
Email:     budi.service@provider.com
Password:  Service@123
Role:      technician
Status:    Active
Name:      Budi Service
Service:   AC Cleaning, Repair
Rating:    4.2/5
Experience: 8 tahun
```

### Technician 3 (Maintenance Specialist)
```
Email:     doni.maintenance@provider.com
Password:  Maintain@123
Role:      technician
Status:    Active
Name:      Doni Maintenance
Service:   AC Maintenance & Installation
Rating:    4.6/5
Experience: 12 tahun
```

### Technician 4 (Junior Technician)
```
Email:     roni.junior@provider.com
Password:  Junior@123
Role:      technician
Status:    Active
Name:      Roni Junior
Service:   AC Cleaning, Basic Repair
Rating:    3.9/5
Experience: 2 tahun
```

### Technician 5 (Experienced)
```
Email:     hendra.pro@provider.com
Password:  ProTech@123
Role:      technician
Status:    Active
Name:      Hendra Professional
Service:   AC Installation, Repair, Maintenance
Rating:    4.9/5
Experience: 20 tahun
```

---

## 🔑 Quick Login Links

### Development Mode (http://localhost:8000)
```
Admin Login:      http://localhost:8000/login
Customer Login:   http://localhost:8000/login
Technician Login: http://localhost:8000/login/technician
Registration:     http://localhost:8000/register
```

### After Login
```
Admin Dashboard:      /dashboard
Customer Dashboard:   /customer/dashboard
Technician Dashboard: /technician/dashboard
```

---

## 🛠️ SQL Commands untuk Create Akun (Backup)

Jika perlu reset akun, jalankan SQL ini:

```sql
-- Create Test Accounts (Truncate existing first)
TRUNCATE TABLE users;

-- ADMIN ACCOUNTS
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Admin Super', 'admin@jasaku.com', '$2y$12$...hash_of_Admin@123456', 'admin', NOW(), NOW(), NOW()),
('Admin Secondary', 'admin2@jasaku.com', '$2y$12$...hash_of_SecurePass123!', 'admin', NOW(), NOW(), NOW());

-- CUSTOMER ACCOUNTS
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Customer One', 'customer1@example.com', '$2y$12$...hash_of_Customer@123', 'customer', NOW(), NOW(), NOW()),
('Budi Customer', 'budi@example.com', '$2y$12$...hash_of_Budi@12345', 'customer', NOW(), NOW(), NOW()),
('Siti Customer', 'siti@example.com', '$2y$12$...hash_of_Siti@12345', 'customer', NOW(), NOW(), NOW()),
('Test Customer', 'test.customer@example.com', '$2y$12$...hash_of_TestPass@123', 'customer', NOW(), NOW(), NOW());

-- TECHNICIAN ACCOUNTS
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Ahmad Teknisi', 'ahmad.teknisi@provider.com', '$2y$12$...hash_of_TechPass@123', 'technician', NOW(), NOW(), NOW()),
('Budi Service', 'budi.service@provider.com', '$2y$12$...hash_of_Service@123', 'technician', NOW(), NOW(), NOW()),
('Doni Maintenance', 'doni.maintenance@provider.com', '$2y$12$...hash_of_Maintain@123', 'technician', NOW(), NOW(), NOW()),
('Roni Junior', 'roni.junior@provider.com', '$2y$12$...hash_of_Junior@123', 'technician', NOW(), NOW(), NOW()),
('Hendra Professional', 'hendra.pro@provider.com', '$2y$12$...hash_of_ProTech@123', 'technician', NOW(), NOW(), NOW());
```

**Catatan:** Password di database harus di-hash menggunakan Laravel's Hash::make()

---

## 🎯 Recommended Testing Sequence

### 1. Admin Access (Test First)
```
1. Login: admin@jasaku.com / Admin@123456
2. Navigate to /dashboard
3. Verify can see all customers, technicians, bookings
4. Test approve payment feature
```

### 2. Customer Workflow (Test Second)
```
1. Login: customer1@example.com / Customer@123
2. Navigate to /customer/dashboard
3. Browse services: /customer/services
4. Create booking: POST /customer/bookings
5. Track booking status
6. Submit payment: POST /customer/invoices/{id}/payment-submit
7. Give rating: POST /customer/bookings/{id}/ratings
```

### 3. Technician Workflow (Test Third)
```
1. Login: ahmad.teknisi@provider.com / TechPass@123
2. Navigate to /technician/dashboard
3. View assigned bookings: /technician/bookings
4. Update status: POST /technician/bookings/{id}/mark-completed
5. View ratings: /technicians/{id}/ratings
```

### 4. Admin Approval (Test Last)
```
1. Back to admin@jasaku.com
2. Approve pending payment: POST /payments/{id}/approve
3. View report: /reports/revenue
4. Verify workflow complete
```

---

## 📊 Account Distribution

| Role | Count | Purpose |
|------|-------|---------|
| Admin | 2 | Dashboard, approvals, reports |
| Customer | 4 | Create bookings, payments, ratings |
| Technician | 5 | Handle bookings, complete work, receive ratings |
| **Total** | **11** | Full workflow testing |

---

## 🔒 Password Policy

All passwords follow this pattern:
```
Format: Type@123456 or Specific@123

Examples:
- Admin@123456
- Customer@123
- TechPass@123
- Service@123

Requirements (if enforced):
✅ Min 8 characters
✅ 1 uppercase letter
✅ 1 lowercase letter
✅ 1 number
✅ Can include special characters
```

---

## ⚡ Quick Commands

### Test Login via Artisan
```bash
# Create test user via seeder
php artisan db:seed --class=UserSeeder

# Create test with specific role
php artisan tinker
>>> User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('TestPass@123'),
    'role' => 'customer'
]);
```

### Reset All Accounts
```bash
# Clear & recreate
php artisan migrate:refresh
php artisan db:seed

# Or specific seeder
php artisan db:seed --class=UsersTableSeeder
```

### Change Password for User
```bash
php artisan tinker
>>> $user = User::where('email', 'admin@jasaku.com')->first();
>>> $user->password = bcrypt('NewPassword@123');
>>> $user->save();
>>> exit
```

---

## 🧪 OAuth Testing Credentials (Optional)

Jika menggunakan OAuth, siapkan aplikasi di:

### Google OAuth
```
Client ID:     [dari Google Console]
Client Secret: [dari Google Console]
Redirect URI:  http://localhost:8000/login/google/callback
Test Email:    testuser@gmail.com
Test Password: [your gmail password]
```

### Facebook OAuth
```
App ID:     [dari Facebook Developer]
App Secret: [dari Facebook Developer]
Redirect:   http://localhost:8000/login/facebook/callback
Test Email: testuser@facebook.com
```

---

## ✅ Pre-Launch Checklist

Sebelum production, pastikan:

- [ ] Ubah semua password ke password yang strong & unique
- [ ] Hapus test accounts (customer1, test.customer, roni.junior)
- [ ] Buat akun admin baru dengan password strong
- [ ] Setup email notifications
- [ ] Configure Midtrans untuk production
- [ ] Update OAuth credentials untuk production
- [ ] Enable HTTPS
- [ ] Setup backup database
- [ ] Configure monitoring & logging

---

## 📞 Support

Akun mana yang ingin digunakan untuk testing?
- Untuk test booking flow: gunakan **customer1@example.com**
- Untuk test technician work: gunakan **ahmad.teknisi@provider.com**
- Untuk test admin approval: gunakan **admin@jasaku.com**

---

**Created:** April 26, 2026
**Status:** Test Accounts Ready
**Last Updated:** April 26, 2026
