# FixHub — Technician Booking System

A web-based technician booking platform that connects customers with repair technicians in San Pablo City, Laguna. Built as a course project demonstrating full CRUD functionality with PHP and MySQL.

---

## Developers

| Name |

Paolo Zarsuelo 
Charles Joward Asio 
Christopher Estrellado 
Gian Arcinas 
John Paul Ong 
Sophia Celo 

**Course:** Bachelor of Science in Computer Engineering  
**Section:** BSCPE 3B  
**Professor:** Engr. Jervhy Ardiente  
**Institution:** San Pablo Colleges


---

## Project Description

FixHub is a technician booking system where customers can find and book repair technicians. The system supports two types of users — customers and technicians — each with their own dashboard and features.

---

## CRUD Functions

| Operation | Feature |

| **CREATE** | Customer registration, Technician registration, Submit booking |
| **READ** | View technicians list, View bookings, Login authentication |
| **UPDATE** | Technician confirms booking, Technician marks booking as done |
| **DELETE** | Customer cancels a booking |

---

## Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local Server:** XAMPP (Apache)
- **Hosting:** InfinityFree (Web), VirtualBox + Ubuntu (Local)
- **Version Control:** GitHub

---

## Project Structure

fixhub/
db.php              # Database connection
index.php           # Login page
register.php        # Customer registration
tech_register.php   # Technician registration
technicians.php     # View all technicians
booking.php         # Book a technician
my_bookings.php     # Customer bookings
tech_dashboard.php  # Technician dashboard
logout.php          # Logout

---

## How To Run Locally

1. Install XAMPP
2. Clone this repository into `C:\xampp\htdocs\fixhub`
3. Start Apache and MySQL in XAMPP
4. Open `localhost/phpmyadmin` and create database `fixhub_db`
5. Import the SQL tables
6. Open `localhost/fixhub` in your browser

---

## Database Tables

| Table | Description |

| `users` | Stores all customer and technician accounts |
| `technicians` | Stores technician details and skills |
| `bookings` | Stores all booking transactions |

---

## Features

### Customer Side
- Register and login
- Browse available technicians
- Search technicians by name or skill
- Book a technician with preferred date
- View and cancel bookings

### Technician Side
- Register and login
- View incoming bookings dashboard
- See booking statistics
- Confirm or mark bookings as done
