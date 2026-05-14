async function pollTicketStatus(ticketId) {
  console.log('pollTicketStatus called with ticketId:', ticketId);
  const el = document.getElementById('ticket-status');
  console.log('Element found:', el);
  if (!el) return;

  try {
    const url = `api/queue_status.php?ticket_id=${encodeURIComponent(ticketId)}`;
    console.log('Fetching:', url);
    const res = await fetch(url, { credentials: 'same-origin' });
    const data = await res.json();
    
    console.log('Response data:', data);

    if (!data.ok) {
      el.innerHTML = `<div class="flash error">${data.error || 'Unable to fetch status'}</div>`;
      return;
    }

    const t = data.ticket;
    console.log('Ticket object:', t);
    console.log('Ticket status value:', t.status);
    console.log('Ticket status type:', typeof t.status);
    const isTerminal = ['SERVED', 'NOSHOW', 'CANCELLED'].includes(t.status);
    console.log('Is terminal:', isTerminal);
    
    let html = `<div class="card">
      <div class="row">
        <div class="col">
          <h2>${t.service_name}</h2>
          <div><span class="badge">Ticket #${t.ticket_no}</span> <span class="badge">Status: ${t.status}</span></div>
          <small>Location: ${t.location}</small>
        </div>
        <div class="col">
          ${t.status === 'WAITING'
            ? `<div><b>People ahead:</b> ${t.ahead}</div><div><b>ETA:</b> ~${t.eta_minutes} min</div>`
            : `<div><b>ETA:</b> 0 min</div>`
          }
          <small>Joined: ${t.joined_at}</small>
        </div>
      </div>
    </div>`;
    
    if (isTerminal) {
      console.log('Adding back to dashboard button');
      html += `<div class="card"><a class="btn" href="user.php">Back to Dashboard</a></div>`;
    }
    
    console.log('Final HTML:', html);
    el.innerHTML = html;
  } catch (e) {
    console.error('Error in pollTicketStatus:', e);
  }
}

function startPolling(ticketId, intervalMs=8000) {
  pollTicketStatus(ticketId);
  setInterval(() => pollTicketStatus(ticketId), intervalMs);
}

async function pollStaffDashboard(serviceId) {
  const url = `api/staff_queue_snapshot.php?service_id=${encodeURIComponent(serviceId)}`;
  const res = await fetch(url, { credentials: 'same-origin' });
  return res.json();
}

const STAFF_DASHBOARD_POLLING_MS = 5000;

function startStaffDashboardPolling(serviceId, intervalMs = STAFF_DASHBOARD_POLLING_MS) {
  if (!serviceId) return;
  let lastSignature = null;
  let inFlight = false;

  const check = async () => {
    if (inFlight) return;
    inFlight = true;
    try {
      const data = await pollStaffDashboard(serviceId);
      if (data && data.ok) {
        if (lastSignature === null) {
          lastSignature = data.signature;
        } else if (lastSignature !== data.signature) {
          const focusedElement = document.querySelector(':is(input, select, textarea, button):focus');
          if (!focusedElement) {
            window.location.reload();
          }
        }
      }
    } catch (e) {
      console.error(e);
    } finally {
      inFlight = false;
    }
  };

  check();
  setInterval(check, intervalMs);
}
