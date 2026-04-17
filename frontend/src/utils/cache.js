const PREFIX = 'pet_store_cache:'

export function setCache(key, value, ttlMs = 60000) {
  const payload = {
    value,
    expiresAt: Date.now() + ttlMs
  }
  localStorage.setItem(`${PREFIX}${key}`, JSON.stringify(payload))
}

export function getCache(key) {
  const raw = localStorage.getItem(`${PREFIX}${key}`)
  if (!raw) return null

  try {
    const payload = JSON.parse(raw)
    if (!payload.expiresAt || Date.now() > payload.expiresAt) {
      localStorage.removeItem(`${PREFIX}${key}`)
      return null
    }
    return payload.value
  } catch (error) {
    localStorage.removeItem(`${PREFIX}${key}`)
    return null
  }
}
