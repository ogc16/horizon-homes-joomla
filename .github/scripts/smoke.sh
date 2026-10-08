#!/usr/bin/env bash
#
# End-to-end smoke test for the Horizon Homes Docker stack.
# Assumes the stack is already up and reachable at $BASE_URL.
#
set -uo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"

pass=0
fail=0

check_status() {
  local path="$1" expected="${2:-200}" label="${3:-$1}" code=""

  for _ in $(seq 1 30); do
    code=$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL$path" || true)
    if [ "$code" = "$expected" ]; then
      printf 'ok   %s  %s\n' "$expected" "$label"
      pass=$((pass + 1))
      return 0
    fi
    sleep 2
  done

  printf 'FAIL want %s got %s  %s\n' "$expected" "$code" "$label"
  fail=$((fail + 1))
}

check_contains() {
  local path="$1" needle="$2" body=""
  body=$(curl -s "$BASE_URL$path")

  if printf '%s' "$body" | grep -q "$needle"; then
    printf "ok   '%s' present in %s\n" "$needle" "$path"
    pass=$((pass + 1))
  else
    printf "FAIL '%s' missing in %s\n" "$needle" "$path"
    fail=$((fail + 1))
  fi
}

LISTINGS="/index.php?option=com_estate&view=listings"
DETAIL="$LISTINGS&alias=modern-4-bedroom-villa-in-karen"
COMPARE="$LISTINGS&task=listings.compare&ids=1,2,3"

echo "=== route checks ($BASE_URL) ==="
check_status "/"                                                    200 "home"
check_status "$LISTINGS"                                            200 "listings"
check_status "$DETAIL"                                              200 "detail (map)"
check_status "$COMPARE"                                             200 "compare"
check_status "/index.php?option=com_estate&task=listings.about"     200 "about"
check_status "/administrator/"                                      200 "admin"

echo "=== content assertions ==="
check_contains "$LISTINGS" "data-compare-id"
check_contains "$LISTINGS" "estate-compare.js"
check_contains "$DETAIL"   "estate-map"
check_contains "$DETAIL"   "data-compare-add"
check_contains "$COMPARE"  "estate-compare__table"

echo "---"
echo "passed: $pass   failed: $fail"

[ "$fail" -eq 0 ]
