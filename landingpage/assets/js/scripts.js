// Demo access form submission
function submitDemoAccess(event) {
    event.preventDefault();
    const formData = {
        name: document.getElementById('demoName').value,
        email: document.getElementById('demoEmail').value,
        whatsapp: document.getElementById('demoWhatsapp').value,
        message: document.getElementById('demoMessage').value
    };
    console.log('Solicitação de acesso à demo enviada:', formData);
    alert('Obrigado! Sua solicitação foi enviada com sucesso.\n\nAnalisaremos sua solicitação e enviaremos as credenciais de acesso via WhatsApp em até 24 horas.');
    document.getElementById('demoAccessForm').reset();
    const modal = bootstrap.Modal.getInstance(document.getElementById('demoAccessModal'));
    modal.hide();
}

// Navbar style on scroll
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        navbar.style.backgroundColor = 'rgba(11, 17, 32, 0.8)';
        navbar.style.backdropFilter = 'blur(10px)';
    } else {
        navbar.style.backgroundColor = 'transparent';
        navbar.style.backdropFilter = 'none';
    }
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const headerOffset = 80;
            const elementPosition = targetElement.offsetTop;
            const offsetPosition = elementPosition - headerOffset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});
