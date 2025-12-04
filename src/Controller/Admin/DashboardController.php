<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\AdminHouse;
use App\Entity\AdminBooking;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('My Booking Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', AdminUser::class);
        yield MenuItem::linkToCrud('Houses', 'fa fa-home', AdminHouse::class);
        yield MenuItem::linkToCrud('Bookings', 'fa fa-list', AdminBooking::class);
    }
}