# Script to integrate infographic section into index.html
$indexFile = "index.html"
$infographicFile = "infographic-section.html"

Write-Host "Reading files..." -ForegroundColor Cyan

# Read the files
$indexContent = Get-Content $indexFile -Raw
$infographicContent = Get-Content $infographicFile -Raw

# Define the pattern to match the old About Section
$pattern = '(?s)        <!-- About Section -->.*?        </section>\r?\n\r?\n        <!-- Team Section -->'

# Create the replacement (infographic + team section marker)
$replacement = $infographicContent + "`r`n`r`n        <!-- Team Section -->"

Write-Host "Replacing About Section with Infographic..." -ForegroundColor Yellow

# Perform the replacement
$newContent = $indexContent -replace $pattern, $replacement

# Backup the original file
Copy-Item $indexFile "$indexFile.backup" -Force
Write-Host "Created backup: $indexFile.backup" -ForegroundColor Green

# Write the new content
$newContent | Set-Content $indexFile -NoNewline

Write-Host "`n✅ Integration Complete!" -ForegroundColor Green
Write-Host "The infographic section has been successfully integrated into index.html" -ForegroundColor Green
Write-Host "`nOpen index.html in your browser to see the beautiful infographic!" -ForegroundColor Cyan
