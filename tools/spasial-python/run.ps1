$ErrorActionPreference = 'Stop'
$taskRoot = (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$taskPython = Join-Path $taskRoot '.venv-spasial/Scripts/python.exe'
if (-not (Test-Path -LiteralPath $taskPython)) {
    python -m venv (Join-Path $taskRoot '.venv-spasial')
    if ($LASTEXITCODE -ne 0) { throw 'Pembuatan virtual environment gagal.' }
}
& $taskPython -m pip install -r (Join-Path $PSScriptRoot 'requirements.txt')
if ($LASTEXITCODE -ne 0) { throw 'Instalasi dependensi gagal.' }
& $taskPython -m streamlit run (Join-Path $PSScriptRoot 'app.py') --server.address 127.0.0.1 --server.port 8501 --server.headless true --browser.gatherUsageStats false
