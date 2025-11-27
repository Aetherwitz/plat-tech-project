(function setupBirthSelects() {
  document.addEventListener('DOMContentLoaded', () => {
    const monthEl = document.getElementById('month');
    const dayEl = document.getElementById('day');
    const yearEl = document.getElementById('year');
    if (!monthEl || !dayEl || !yearEl) return;

    const currentYear = new Date().getFullYear();
    const months = [
      { v: '01', t: 'Jan' }, { v: '02', t: 'Feb' }, { v: '03', t: 'Mar' },
      { v: '04', t: 'Apr' }, { v: '05', t: 'May' }, { v: '06', t: 'Jun' },
      { v: '07', t: 'Jul' }, { v: '08', t: 'Aug' }, { v: '09', t: 'Sep' },
      { v: '10', t: 'Oct' }, { v: '11', t: 'Nov' }, { v: '12', t: 'Dec' }
    ];

    // populate months
    months.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m.v;
      opt.textContent = m.t;
      monthEl.appendChild(opt);
    });

    // populate years (current down to 1950)
    for (let y = currentYear; y >= 1950; y--) {
      const opt = document.createElement('option');
      opt.value = String(y);
      opt.textContent = String(y);
      yearEl.appendChild(opt);
    }

    // compute days in month/year
    function daysInMonth(year, month) {
      return new Date(year, month, 0).getDate(); 
    }

    function rebuildDays() {
      const prevSelected = dayEl.value;
      const monthVal = monthEl.value;
      const yearVal = yearEl.value || String(currentYear);
      const monthNum = monthVal ? parseInt(monthVal, 10) : 1;
      const numDays = daysInMonth(parseInt(yearVal, 10), monthNum);

      Array.from(dayEl.options).forEach(opt => {
        if (opt.value !== '') opt.remove();
      });

      for (let d = 1; d <= numDays; d++) {
        const opt = document.createElement('option');
        opt.value = String(d);
        opt.textContent = String(d);
        dayEl.appendChild(opt);
      }

      
      if (prevSelected && Number(prevSelected) <= numDays) dayEl.value = prevSelected;
      else dayEl.value = '';
    }

    monthEl.addEventListener('change', rebuildDays);
    yearEl.addEventListener('change', rebuildDays);

    
    rebuildDays();
  });
})();