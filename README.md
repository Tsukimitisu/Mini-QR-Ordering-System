# Gourmet Express - Mini QR Ordering System

Gourmet Express is a lightweight, mobile-first restaurant ordering system designed for local prototyping. Customers scan a tabletop QR code, view the live digital menu, add items to their shopping cart, place their order, and simulate a mock checkout payment. Admins can view orders in real time, monitor restaurant-wide statistics, update order/payment statuses via AJAX, and generate/print table tent QR cards.

---

## Technical Stack
- **Frontend**: HTML5, Vanilla CSS3 (Custom Variables & Layouts), Modern JS (ES6+), Bootstrap 5, Bootstrap Icons.
- **Backend API**: PHP (PDO-based database query layer, Transactions, server-side math calculations).
- **Database**: MySQL.
- **Support Utilities**: Node.js (offline static QR generator using the npm `qrcode` package).
- **Local Dev Server**: XAMPP (Apache + MySQL).

---

## Core Features
1. **Interactive Mobile Ordering Menu**: Auto-grouped by categories, disabled out-of-stock items, floating sticky cart, and live cart adjustments.
2. **Mock Payment Terminal**: Customer-facing simulated payment modal with gateway connecting animations, Success vs. Failure outcomes, and order confirmation slips.
3. **Admin Dashboard Controls**: Executive indicators (Total Orders, Pending Orders, Paid Orders, Total Sales), status filtering, real-time AJAX dropdown updates, and itemized lists of purchased products.
4. **Table Card QR Generator**: Interactive tabletop card preview builder allowing immediate card printing with customized target URL parameters.
5. **Security and Math Calculations**: Strictly validates inputs and recalculates all pricing totals directly from database records on the backend.

---

## Installation & Setup Instructions (XAMPP Local Server)

### Step 1: Clone or Copy Files to XAMPP htdocs
Copy the entire `Mini-Ordering-System` folder to your XAMPP installation directory under the `htdocs` directory:
- **Default path on Windows**: `C:\xampp\htdocs\Mini-Ordering-System`

### Step 2: Start Apache and MySQL in XAMPP
1. Open the **XAMPP Control Panel** from your computer.
2. Click the **Start** button next to **Apache**.
3. Click the **Start** button next to **MySQL**.
4. Ensure both modules are highlighted green.

### Step 3: Setup the Database
1. Open your browser and navigate to: `http://localhost/phpmyadmin/`
2. Click on the **SQL** tab at the top.
3. Open the file `database/mini_qr_ordering_db.sql` in a text editor, copy its contents, paste them into the phpMyAdmin SQL text box, and click **Go**.
   - Alternatively: Click the **Import** tab in phpMyAdmin, choose the SQL file located at `c:\xampp\htdocs\Mini-Ordering-System\database\mini_qr_ordering_db.sql`, and click **Import** at the bottom.
4. This will create the database `mini_qr_ordering_db` and insert the sample tables (`products`, `orders`, `order_items`, `tables`) along with gourmet seed items.

### Step 4: Run Node.js QR Exporter (Optional)
The system includes a web-based QR generator, but if you want to batch generate static high-resolution QR codes to the files system:
1. Open your terminal/command prompt.
2. Navigate to the project root:
   ```bash
   cd c:\xampp\htdocs\Mini-Ordering-System
   ```
3. Install dependencies:
   ```bash
   npm install
   ```
4. Run the generator script:
   ```bash
   npm run generate-qrs
   ```
5. Check the generated PNGs in `assets/images/qrcodes/` (from Table 1 to 10).

---

## Testing the System Flow

1. **Access the Portal Page**:
   Open your browser and navigate to: `http://localhost/Mini-Ordering-System/`

2. **Customer Flow**:
   - Click the **Open Menu (Table 1)** button. This will simulate scanning a QR code for Table 1 (`http://localhost/Mini-Ordering-System/customer/order.php?table=1`).
   - Add some items (e.g. Classic Cheeseburger, French Fries) to your cart.
   - Adjust quantities or remove items, checking that totals auto-compute.
   - Enter your name (e.g., "John Doe") and table number, then click **Place Order**.
   - A mock payment screen will appear. Select **Simulate Payment Success** or **Simulate Payment Failed** to verify the response.
   - On success, the cart clears, showing your receipt.

3. **Admin Flow**:
   - Go back to the homepage portal (`http://localhost/Mini-Ordering-System/`) and click **Open Admin Dashboard** (or go to `http://localhost/Mini-Ordering-System/admin/dashboard.php`).
   - Monitor the order you just created! Try changing the **Order Status** dropdown (e.g., to "Preparing" or "Completed") or **Payment Status** (e.g., to "Paid") and notice how status badges update color dynamically and statistics refresh immediately.
   - Filter orders by clicking the filter pill buttons.
   - Click on the **QR Generator** button in the header, input a table number, and print the generated layout card.
