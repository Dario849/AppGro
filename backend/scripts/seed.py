"""Seed test data for development and testing."""

from sqlmodel import Session, create_engine
from app.db import engine
from app.models import (
    Organization, User, Sector, Lote, Task, Livestock, Crop,
    CropTreatment, AccountingTransaction, WeatherObservation, Asset
)


def seed_database():
    """Populate test data."""
    with Session(engine) as session:
        # Organization
        org = Organization(
            name="Demo Farm",
            email="farm@demo.local",
            address="123 Rural Road, Country State",
            phone="555-0100",
        )
        session.add(org)
        session.commit()
        session.refresh(org)

        # Users
        user_admin = User(
            organization_id=org.id,
            email="admin@demo.local",
            hashed_password="$2b$12$...",  # placeholder
            full_name="Admin User",
            role="admin",
        )
        user_manager = User(
            organization_id=org.id,
            email="manager@demo.local",
            hashed_password="$2b$12$...",
            full_name="Farm Manager",
            role="manager",
        )
        user_operator = User(
            organization_id=org.id,
            email="operator@demo.local",
            hashed_password="$2b$12$...",
            full_name="Field Operator",
            role="operator",
        )
        for user in [user_admin, user_manager, user_operator]:
            session.add(user)
        session.commit()

        # Sectors
        sector_north = Sector(
            organization_id=org.id,
            name="North Field",
            area_hectares=50.0,
            location_notes="GPS: 40.7128° N",
        )
        sector_south = Sector(
            organization_id=org.id,
            name="South Field",
            area_hectares=35.0,
            location_notes="GPS: 40.7000° N",
        )
        for sector in [sector_north, sector_south]:
            session.add(sector)
        session.commit()

        # Lotes (plots)
        lote_n1 = Lote(
            organization_id=org.id,
            sector_id=sector_north.id,
            name="North-A",
            area_hectares=25.0,
            soil_type="loamy",
        )
        lote_n2 = Lote(
            organization_id=org.id,
            sector_id=sector_north.id,
            name="North-B",
            area_hectares=25.0,
            soil_type="loamy",
        )
        for lote in [lote_n1, lote_n2]:
            session.add(lote)
        session.commit()

        # Tasks
        task1 = Task(
            organization_id=org.id,
            title="Prepare North Field",
            description="Plow and prepare soil for planting",
            priority=1,
            status="in_progress",
            assigned_to_user_id=user_operator.id,
            due_date="2026-04-30",
            sector_id=sector_north.id,
        )
        task2 = Task(
            organization_id=org.id,
            title="Plant Soybeans",
            description="Plant soybean crop in North-A",
            priority=2,
            status="pending",
            assigned_to_user_id=user_operator.id,
            due_date="2026-05-05",
            sector_id=sector_north.id,
            lote_id=lote_n1.id,
        )
        for task in [task1, task2]:
            session.add(task)
        session.commit()

        # Livestock
        livestock1 = Livestock(
            organization_id=org.id,
            identifier="001",
            animal_type="bovine",
            breed="Angus",
            birth_date="2023-06-15",
            weight_kg=450.0,
        )
        livestock2 = Livestock(
            organization_id=org.id,
            identifier="002",
            animal_type="bovine",
            breed="Angus",
            birth_date="2023-07-20",
            weight_kg=420.0,
        )
        for livestock in [livestock1, livestock2]:
            session.add(livestock)
        session.commit()

        # Crops
        crop1 = Crop(
            organization_id=org.id,
            crop_type="soybean",
            sector_id=sector_north.id,
            lote_id=lote_n1.id,
            planting_date="2026-05-10",
            quantity_planted=100.0,
            unit="kg",
        )
        session.add(crop1)
        session.commit()

        # Crop treatments
        treatment1 = CropTreatment(
            organization_id=org.id,
            crop_id=crop1.id,
            treatment_type="fertilizer",
            treatment_date="2026-05-15",
            product_name="NPK 15-15-15",
            quantity=500.0,
        )
        session.add(treatment1)
        session.commit()

        # Accounting transactions
        transaction1 = AccountingTransaction(
            organization_id=org.id,
            transaction_type="expense",
            amount=5000.00,
            category="seeds",
            transaction_date="2026-04-20",
            description="Soybean seed purchase",
        )
        transaction2 = AccountingTransaction(
            organization_id=org.id,
            transaction_type="expense",
            amount=1200.00,
            category="equipment",
            transaction_date="2026-04-18",
            description="Fuel for tractor",
        )
        for transaction in [transaction1, transaction2]:
            session.add(transaction)
        session.commit()

        # Weather observations
        weather1 = WeatherObservation(
            organization_id=org.id,
            sector_id=sector_north.id,
            observation_date="2026-04-23",
            weather_type="rain",
            notes="Light rain, good for crops",
        )
        session.add(weather1)
        session.commit()

        # Assets
        asset1 = Asset(
            organization_id=org.id,
            name="John Deere Tractor",
            asset_type="equipment",
            purchase_date="2022-01-15",
            status="active",
        )
        session.add(asset1)
        session.commit()

        print("✓ Seed data loaded successfully")
        print(f"  Organization: {org.name} (id={org.id})")
        print(f"  Users: 3")
        print(f"  Sectors: 2")
        print(f"  Lotes: 2")
        print(f"  Tasks: 2")
        print(f"  Livestock: 2")
        print(f"  Crops: 1")
        print(f"  Crop Treatments: 1")
        print(f"  Transactions: 2")
        print(f"  Weather Observations: 1")
        print(f"  Assets: 1")


if __name__ == "__main__":
    seed_database()
