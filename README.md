## 📜 License

This project is proprietary software.

The source code is publicly viewable for demonstration purposes only.

No permission is granted to copy, modify, redistribute, or use this project or its source code without explicit written permission from the copyright holder.

© 2026 ResQFood. All Rights Reserved.

# 🍽️ ResQFood
 
> **Rescuing Food. Feeding Futures.**
 
ResQFood is a platform tackling food waste in Bangladesh by connecting surplus food from restaurants, bakeries, event caterers, and grocery stores with the NGOs, community kitchens, and volunteers who can put it to use before it's wasted.
 
---
 
## 🌍 The Problem
 
Bangladesh generates significant food waste every year while many households continue to face food insecurity. Restaurants, bakeries, supermarkets, and event organizers often discard perfectly edible surplus food simply because there's no efficient way to redistribute it before it expires. Existing food delivery platforms are built for fresh, prepared meals — not surplus recovery.
 
---
 
## ✅ Live MVP — What's Actually Running Today
 
This repository contains a working prototype: a donation-based food recovery marketplace connecting **donors** (restaurants, caterers, individuals with surplus food) directly with **NGOs/volunteers** who claim and pick it up. No payment is involved in this version — it's a free donation and logistics-matching flow.
 
**Built with:** PHP, MySQL (mysqli, prepared statements throughout), vanilla JS/CSS. No framework dependencies — runs on any standard LAMP stack.
 
### Working features
 
**Donor Portal**
- Registration & secure login (bcrypt-hashed passwords)
- Post surplus food listings — type, quantity, pickup window, location
- Edit / delete own listings
- Dashboard showing listing status (available, claimed, completed)
**NGO / Volunteer Portal**
- Registration & secure login
- Browse all currently available listings
- Claim a listing (race-condition-safe — two NGOs can't double-claim the same listing)
- Mark claimed pickups as completed
- Dashboard of active and past claims
**Admin**
- View all users and all listings across the platform
- Monitor claim status system-wide
**Data model:** `users` (donor / ngo / admin roles), `food_listings` (type, quantity, pickup window, status), `claims` (linking listings to the NGO that claimed them, with status tracking).
 
**Security posture:** parameterized queries throughout (no SQL injection surface), hashed passwords, output escaping (`htmlspecialchars`) against XSS, server-side validation on all forms, session-based role guards on every protected page.
 
### Known gaps in the current build
- No CSRF tokens yet on POST forms
- No login rate-limiting
- No photo uploads on listings
- No payments — this version is 100% donation-based, no transactions
---
 
## 🚀 Product Vision & Roadmap
 
The MVP proves the core matching loop works. The longer-term product widens the model beyond pure donation into a full surplus-recovery ecosystem:
 
- **Discount marketplace layer** — vendors can optionally sell near-expiry surplus at a discount to nearby consumers (not just donate), recovering some revenue while still cutting waste. Remaining unsold surplus routes to NGO partners automatically.
- **Payments** — bKash / Nagad integration for the marketplace layer.
- **Logistics** — route optimization for volunteers handling multiple pickups, SMS access for low-connectivity users.
- **Trust & transparency** — photo verification on listings, ratings/reviews, and an auditable donation trail.
- **Intelligence layer** — demand prediction and waste analytics to help donors know what surplus is coming and help NGOs plan capacity.
The MVP in this repo is the foundation this roadmap builds on — the data model (donors, listings, claims) is designed to extend into the marketplace and payments layer without a rewrite.
