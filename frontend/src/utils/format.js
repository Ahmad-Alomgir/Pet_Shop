export function formatBDT(value) {
  const amount = Number(value || 0)
  return new Intl.NumberFormat('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
