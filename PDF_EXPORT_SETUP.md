# PDF Export Setup Guide

## Get Your Free API Key

1. Go to https://pdfshift.io/register
2. Sign up for a free account (50 PDFs/month free)
3. After signing up, you'll get your API key
4. Copy the API key

## Install the API Key

1. Open `generate_report_pdf.php`
2. Find this line:
   ```php
   $api_key = 'YOUR_PDFSHIFT_API_KEY';
   ```
3. Replace `YOUR_PDFSHIFT_API_KEY` with your actual key:
   ```php
   $api_key = 'sk_abc123...'; // Your actual key
   ```
4. Save the file

## How to Use

1. Go to Reports page
2. Select a date from the calendar
3. Click "Export to PDF" button
4. PDF will download automatically!

## Features

The PDF includes:
- ✅ Company header with logo
- ✅ Selected date/period
- ✅ Summary (Total Profit, Expense, Net Income)
- ✅ Complete financial records table
- ✅ Professional formatting with your brand colors
- ✅ Generated timestamp

## Alternative: Free Option Without API

If you don't want to use an API, you can use the browser's print function:

Add this button instead:
```html
<button onclick="window.print()" class="btn btn-gold">
    <i class="fa-solid fa-print me-2"></i>Print Report
</button>
```

Users can then "Save as PDF" from the print dialog!

## Troubleshooting

**Error: "API key invalid"**
- Make sure you copied the entire API key
- Check there are no extra spaces

**Error: "Limit exceeded"**
- Free plan has 50 PDFs/month
- Wait until next month or upgrade plan

**PDF not downloading**
- Check your browser's download settings
- Try a different browser
