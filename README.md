# 💼 UKK Job Seeker API

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)

> **UKK 2025 - SMK Telkom Malang**  
> RESTful API untuk platform pencarian kerja yang menghubungkan perusahaan dengan pencari kerja (Society).

---

## 📖 Tentang Project

**Job Seeker API** adalah backend REST API berbasis **Laravel 11** untuk sistem pencarian kerja. API ini mendukung dua jenis pengguna:
- **HRD (Company)**: Dapat membuat profil perusahaan, posting lowongan pekerjaan, dan mengelola lamaran.
- **Society (Job Seeker)**: Dapat membuat profil, menambahkan portfolio, mencari lowongan, dan melamar pekerjaan.

Dilengkapi dengan autentikasi menggunakan **Laravel Sanctum** untuk keamanan API.

---

## ✨ Features

### 🏢 **HRD/Company Features:**
- ✅ Register & Login sebagai HRD
- ✅ Membuat & update profil perusahaan
- ✅ Membuat, edit, dan hapus lowongan pekerjaan
- ✅ Melihat daftar pelamar per posisi
- ✅ Approve/Reject lamaran (PENDING, ACCEPTED, REJECTED)

### 👤 **Society/Job Seeker Features:**
- ✅ Register & Login sebagai Society
- ✅ Membuat & update profil pribadi
- ✅ Menambahkan & mengelola portfolio
- ✅ Melihat lowongan pekerjaan yang aktif
- ✅ Apply lowongan pekerjaan
- ✅ Track status lamaran

---

## 🧩 Tech Stack

- **Backend Framework**: Laravel 11
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum (Bearer Token)
- **API Testing**: Postman / Thunder Client

---

## ⚙️ Installation

### **Prerequisites:**
- PHP >= 8.2
- Composer
- MySQL
- XAMPP / Laragon (untuk local development)

### **Installation Steps:**
```bash
# 1. Clone repository
git clone https://github.com/stgatanstn/ukk-job-seeker-api.git
cd ukk-job-seeker-api

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_seeker_db
DB_USERNAME=root
DB_PASSWORD=

# 6. Create database
# Buat database 'job_seeker_db' di phpMyAdmin atau MySQL

# 7. Run migrations
php artisan migrate

# 8. Start development server
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

---

## 📡 API Endpoints

### **Authentication**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/register` | Register user (HRD/Society) | ✅ |
| POST | `/api/login` | Login user | ✅ |

### **HRD - Company Management**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/hrd/profilehrd` | Create/Update company profile | ✅ |
| GET | `/api/companies/read-me` | Get my company | ✅ |
| GET | `/api/companies/read` | Get all companies | ✅ |

### **HRD - Position Management**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/hrd/insert` | Create job position | ✅ |
| GET | `/api/hrd/get` | Get my positions | ✅ |
| POST | `/api/hrd/update/{id}` | Update position | ✅ |
| DELETE | `/api/hrd/delete/{id}` | Delete position | ✅ |

### **Society - Profile & Portfolio**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/society/profile` | Create/Update profile | ✅ |
| GET | `/api/societies/read-me` | Get my profile | ✅ |
| POST | `/api/society/portofolio` | Add portfolio | ✅ |
| GET | `/api/society/portofolio` | Get my portfolios | ✅ |
| POST | `/api/society/portofolio/{id}` | Update portfolio | ✅ |
| DELETE | `/api/society/portofolio/{id}` | Delete portfolio | ✅ |

### **Job Application**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/hrd/get` | View available positions (Society) | ✅ |
| POST | `/api/applied/create` | Apply for job | ✅ |
| GET | `/api/applied/my` | Get my applications (Society) | ✅ |
| GET | `/api/applied/by-position/{id}` | View applicants (HRD) | ✅ |
| POST | `/api/applied/update-status/{id}` | Update application status (HRD) | ✅ |

**Full API Documentation:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 🗄️ Database Schema

### **Main Tables:**
- `users` - User authentication data
- `company` - Company profiles (HRD)
- `society` - Job seeker profiles
- `available_position` - Job positions
- `portofolio` - Society portfolios
- `position_applied` - Job applications

**Full Database Schema:** [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)

---

## 🧪 Testing

### **Import Postman Collection:**
1. Download: [UKK-JobSeeker-Postman.json](docs/UKK-JobSeeker-Postman.json)
2. Import ke Postman
3. Setup environment variable `token`
4. Test semua endpoints!

### **Quick Test Flow:**

**HRD Flow:**
```bash
1. POST /api/register (role: HRD)
2. POST /api/hrd/profilehrd (create company)
3. POST /api/hrd/insert (create position)
```

**Society Flow:**
```bash
1. POST /api/register (role: Society)
2. POST /api/society/profile (create profile)
3. POST /api/society/portofolio (add portfolio)
4. POST /api/applied/create (apply job)
```

---

## 📁 Project Structure
```
ukk-job-seeker-api/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── CompanyController.php
│   │   ├── SocietyController.php
│   │   ├── PositionController.php
│   │   ├── PortofolioController.php
│   │   └── PositionAppliedController.php
│   └── Models/
│       ├── User.php
│       ├── Company.php
│       ├── Society.php
│       ├── AvailablePosition.php
│       ├── Portofolio.php
│       └── PositionApplied.php
├── database/
│   └── migrations/
├── routes/
│   ├── api.php
│   └── web.php
├── .env.example
└── README.md
```

---

## 🔐 Authentication

API menggunakan **Laravel Sanctum** dengan Bearer Token.

**Cara menggunakan:**
1. Register atau Login untuk mendapatkan token
2. Simpan token dari response
3. Gunakan token di header:
```
   Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 🎓 Development Notes

- Project ini dibuat untuk **UKK (Uji Kompetensi Keahlian) 2025**
- Mengikuti design pattern **MVC** dan **RESTful API** best practices
- Database design mengikuti **normalisasi** dan **relational database** principles
- API responses mengikuti **standard JSON format**

---

## 👨‍💻 Author

**Gilson Aristo Nagatan**  
RPL XII - SMK Telkom Malang  
UKK 2025 - Rekayasa Perangkat Lunak

📧 Email: [gilson.dokumenkerja@gmail.com]  
🔗 GitHub: [@stgatanstn](https://github.com/stgatanstn)

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

## 🙏 Acknowledgments

- **Laravel Framework** - Backend framework
- **SMK Telkom Malang** - Educational institution
- **Pembimbing UKK** - Guidance and support

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan buka [GitHub Issues](https://github.com/stgatanstn/ukk-job-seeker-api/issues).

---

**⭐ Jika project ini membantu, berikan star di GitHub!**