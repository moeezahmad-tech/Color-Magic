#!/usr/bin/env python3
"""
ColorMagic API Comprehensive Endpoint Test Suite
Tests all V2 and V1 endpoints against https://colormagic-api.techkreative.com/ (or custom host).

Usage:
    python test_api.py
    python test_api.py http://colormagic-api.techkreative.com
    python test_api.py --insecure --verbose
"""

import sys
import time
import json
import ssl
import urllib.request
import urllib.error
import urllib.parse
from typing import Dict, Any, Optional, Tuple

# Fix Windows console UTF-8 output
if hasattr(sys.stdout, "reconfigure"):
    try:
        sys.stdout.reconfigure(encoding="utf-8")
    except Exception:
        pass

DEFAULT_BASE_URL = "https://colormagic-api.techkreative.com"

# Colors for terminal output
GREEN = "\033[92m"
RED = "\033[91m"
YELLOW = "\033[93m"
CYAN = "\033[96m"
BOLD = "\033[1m"
RESET = "\033[0m"

# Enable ANSI on Windows terminal if possible
if sys.platform == "win32":
    import os
    os.system("")

# Create unverified SSL context for local/staging/self-signed cert testing
SSL_CONTEXT = ssl.create_default_context()
SSL_CONTEXT.check_hostname = False
SSL_CONTEXT.verify_mode = ssl.CERT_NONE

def make_request(
    url: str,
    method: str = "GET",
    data: Optional[Dict[str, Any]] = None,
    headers: Optional[Dict[str, str]] = None,
    timeout: int = 15,
    insecure: bool = True
) -> Tuple[int, Optional[Dict[str, Any]], Dict[str, str], float, Optional[str]]:
    """Execute HTTP request and return (status_code, json_data, headers, latency_ms, error_msg)"""
    if headers is None:
        headers = {}
    
    headers.setdefault("User-Agent", "ColorMagic-TestRunner/2.0")
    headers.setdefault("Accept", "application/json")
    headers.setdefault("Cache-Control", "no-cache, no-store, must-revalidate")
    headers.setdefault("Pragma", "no-cache")

    # Append cache-busting timestamp parameter to bypass intermediate CDN caching
    sep = '&' if '?' in url else '?'
    url = f"{url}{sep}_cb={int(time.time() * 1000)}"

    encoded_data = None
    if data is not None:
        encoded_data = json.dumps(data).encode("utf-8")
        headers["Content-Type"] = "application/json; charset=UTF-8"

    req = urllib.request.Request(url, data=encoded_data, headers=headers, method=method)
    ctx = SSL_CONTEXT if (insecure and url.startswith("https://")) else None

    start_time = time.perf_counter()
    try:
        with urllib.request.urlopen(req, timeout=timeout, context=ctx) as response:
            latency_ms = (time.perf_counter() - start_time) * 1000
            status_code = response.getcode()
            res_headers = dict(response.info())
            raw_body = response.read().decode("utf-8", errors="replace")
            
            try:
                parsed_json = json.loads(raw_body)
            except Exception:
                parsed_json = None

            return status_code, parsed_json, res_headers, latency_ms, None
    except urllib.error.HTTPError as e:
        latency_ms = (time.perf_counter() - start_time) * 1000
        raw_body = e.read().decode("utf-8", errors="replace")
        try:
            parsed_json = json.loads(raw_body)
        except Exception:
            parsed_json = None
        return e.code, parsed_json, dict(e.headers or {}), latency_ms, f"HTTPError {e.code}"
    except Exception as e:
        latency_ms = (time.perf_counter() - start_time) * 1000
        return 0, None, {}, latency_ms, str(e)


def run_test_suite(base_url: str, verbose: bool = False, insecure: bool = True) -> bool:
    base_url = base_url.rstrip("/")
    print(f"\n{BOLD}{CYAN}======================================================{RESET}")
    print(f"{BOLD}{CYAN}   ColorMagic API Comprehensive Endpoint Test Suite   {RESET}")
    print(f"{BOLD}{CYAN}======================================================{RESET}")
    print(f"Target API: {BOLD}{base_url}{RESET}")
    print(f"SSL Mode:   {'Insecure/Self-Signed OK' if insecure else 'Strict Verified'}\n")

    endpoints_to_test = [
        # --- V2 Health & Discovery ---
        {
            "category": "V2 System & Diagnostics",
            "name": "V2 Health Check",
            "path": "/v2/health",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("status") == "healthy"
        },
        {
            "category": "V2 System & Diagnostics",
            "name": "Shorthand Health Check",
            "path": "/health",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success"
        },
        {
            "category": "V2 System & Diagnostics",
            "name": "Root Discovery Index",
            "path": "/",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and "v2_endpoints" in j
        },

        # --- V2 Colors ---
        {
            "category": "V2 Colors API",
            "name": "Colors Paginated List",
            "path": "/v2/colors?page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and len(j.get("data", [])) == 10
        },
        {
            "category": "V2 Colors API",
            "name": "Color by Hex (/v2/colors/123524)",
            "path": "/v2/colors/123524",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "phthalo green"
        },
        {
            "category": "V2 Colors API",
            "name": "Color by Hex (/v2/colors/FF0000)",
            "path": "/v2/colors/FF0000",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("raw_hex", "").upper() == "FF0000"
        },
        {
            "category": "V2 Colors API",
            "name": "Shorthand Color URL (/color/123524)",
            "path": "/color/123524",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "phthalo green"
        },
        {
            "category": "V2 Colors API",
            "name": "Color by Slug (/v2/colors/slug/phthalo-green)",
            "path": "/v2/colors/slug/phthalo-green",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("raw_hex", "").upper() == "123524"
        },
        {
            "category": "V2 Colors API",
            "name": "Search Colors by Query (?q=blue)",
            "path": "/v2/colors?q=blue&page=1&limit=5",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },
        {
            "category": "V2 Colors API",
            "name": "Colors Dictionary Format (?format=dict)",
            "path": "/v2/colors?format=dict",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and "123524" in j.get("data", {})
        },

        # --- V2 Gradients ---
        {
            "category": "V2 Gradients API",
            "name": "Gradients Paginated List",
            "path": "/v2/gradients?page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and len(j.get("data", [])) == 10
        },
        {
            "category": "V2 Gradients API",
            "name": "Single Gradient by ID (/v2/gradients/gradient_1)",
            "path": "/v2/gradients/gradient_1",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "sunset blaze"
        },
        {
            "category": "V2 Gradients API",
            "name": "Filter Gradients by Style (?style=Warm)",
            "path": "/v2/gradients?style=Warm&page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },
        {
            "category": "V2 Gradients API",
            "name": "Filter Gradients by Type (?type=radial)",
            "path": "/v2/gradients?type=radial&page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },
        {
            "category": "V2 Gradients API",
            "name": "Search Gradients (?q=sunset)",
            "path": "/v2/gradients?q=sunset",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },

        # --- V2 Palettes ---
        {
            "category": "V2 Palettes API",
            "name": "Palettes Paginated List",
            "path": "/v2/palettes?page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and len(j.get("data", [])) == 10
        },
        {
            "category": "V2 Palettes API",
            "name": "Single Palette by ID (/v2/palettes/palette_1)",
            "path": "/v2/palettes/palette_1",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "forest breath"
        },
        {
            "category": "V2 Palettes API",
            "name": "Filter Palettes by Style (?style=Eco)",
            "path": "/v2/palettes?style=Eco&page=1&limit=10",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },
        {
            "category": "V2 Palettes API",
            "name": "Search Palettes (?q=forest)",
            "path": "/v2/palettes?q=forest",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("total", 0) > 0
        },
        {
            "category": "V2 Palettes API",
            "name": "Submit Community Palette (POST /v2/palettes)",
            "path": "/v2/palettes",
            "method": "POST",
            "payload": {
                "name": "Aurora Test",
                "style": "Neon",
                "colors": ["#00F0FF", "#7000FF", "#FF007B"]
            },
            "expected_status": 201,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("status") == "pending"
        },

        # --- V1 Backward Compatibility ---
        {
            "category": "V1 Legacy Endpoints",
            "name": "V1 Health (/v1/health)",
            "path": "/v1/health",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success"
        },
        {
            "category": "V1 Legacy Endpoints",
            "name": "V1 Colors by Hex (?hex=123524)",
            "path": "/v1/colors?hex=123524",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "phthalo green"
        },
        {
            "category": "V1 Legacy Endpoints",
            "name": "V1 Colors by Slug (?slug=phthalo-green)",
            "path": "/v1/colors?slug=phthalo-green",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and (j.get("data", {}).get("hex") in ("#123524", "123524"))
        },
        {
            "category": "V1 Legacy Endpoints",
            "name": "V1 Gradients by ID (?id=gradient_1)",
            "path": "/v1/gradients?id=gradient_1",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "sunset blaze"
        },
        {
            "category": "V1 Legacy Endpoints",
            "name": "V1 Palettes by ID (?id=palette_1)",
            "path": "/v1/palettes?id=palette_1",
            "expected_status": 200,
            "validate": lambda j: j.get("status") == "success" and j.get("data", {}).get("name", "").lower() == "forest breath"
        },
    ]

    current_category = ""
    passed_count = 0
    failed_count = 0
    total_latency = 0.0

    for test in endpoints_to_test:
        cat = test.get("category", "")
        if cat != current_category:
            current_category = cat
            print(f"\n{BOLD}[Category] {current_category}{RESET}")

        url = base_url + test["path"]
        method = test.get("method", "GET")
        payload = test.get("payload")
        expected_code = test.get("expected_status", 200)

        code, jdata, headers, lat, err = make_request(url, method=method, data=payload, insecure=insecure)
        total_latency += lat

        is_passed = True
        fail_reasons = []

        if err:
            is_passed = False
            fail_reasons.append(f"Network/Error: {err}")
        elif code != expected_code:
            is_passed = False
            fail_reasons.append(f"HTTP Status {code} != Expected {expected_code}")

        if jdata is None and not err:
            is_passed = False
            fail_reasons.append("Response is not valid JSON")

        if is_passed and jdata is not None and "validate" in test:
            try:
                if not test["validate"](jdata):
                    is_passed = False
                    fail_reasons.append("Validation assertion failed on response payload")
            except Exception as ve:
                is_passed = False
                fail_reasons.append(f"Validation exception: {ve}")

        status_tag = f"{GREEN}[PASS]{RESET}" if is_passed else f"{RED}[FAIL]{RESET}"
        lat_tag = f"{CYAN}{lat:.1f}ms{RESET}"
        print(f"  {status_tag} {test['name']:<45} {lat_tag}")

        if is_passed:
            passed_count += 1
        else:
            failed_count += 1
            print(f"         {RED}-> URL: {url}{RESET}")
            for r in fail_reasons:
                print(f"         {RED}-> Error: {r}{RESET}")
            if verbose and jdata:
                print(f"         {YELLOW}-> Response: {json.dumps(jdata, indent=2)[:300]}...{RESET}")

    total_tests = len(endpoints_to_test)
    avg_latency = total_latency / total_tests if total_tests > 0 else 0

    print(f"\n{BOLD}------------------------------------------------------{RESET}")
    print(f"  {BOLD}Test Results Summary:{RESET}")
    print(f"  Passed: {GREEN}{passed_count}{RESET} / {total_tests}")
    if failed_count > 0:
        print(f"  Failed: {RED}{failed_count}{RESET} / {total_tests}")
    print(f"  Avg Latency: {CYAN}{avg_latency:.1f} ms{RESET}")
    print(f"{BOLD}------------------------------------------------------{RESET}\n")

    if failed_count == 0:
        print(f"{GREEN}{BOLD}All endpoints tested and verified operational!{RESET}\n")
        return True
    else:
        print(f"{RED}{BOLD}Some endpoints failed. Review errors above.{RESET}\n")
        return False


if __name__ == "__main__":
    target = DEFAULT_BASE_URL
    verbose_flag = False
    insecure_flag = True

    args = sys.argv[1:]
    for a in args:
        if a in ("-v", "--verbose"):
            verbose_flag = True
        elif a in ("--strict", "--secure"):
            insecure_flag = False
        elif not a.startswith("-"):
            target = a

    success = run_test_suite(target, verbose=verbose_flag, insecure=insecure_flag)
    sys.exit(0 if success else 1)
