"""Initial: 12 core entities with org scoping and audit

Revision ID: 001_initial
Revises: 
Create Date: 2026-04-23 17:40:00.000000

"""
from alembic import op
import sqlalchemy as sa
from sqlmodel import SQLModel


# revision identifiers, used by Alembic.
revision = '001_initial'
down_revision = None
branch_labels = None
depends_on = None


def upgrade() -> None:
    # Create organization (root tenant)
    op.create_table(
        'organization',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('name', sa.String(), nullable=False),
        sa.Column('email', sa.String(), nullable=True),
        sa.Column('address', sa.String(), nullable=True),
        sa.Column('phone', sa.String(), nullable=True),
        sa.Column('is_active', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_organization_name'), 'organization', ['name'], unique=False)

    # Create user (org scoped)
    op.create_table(
        'user',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('email', sa.String(), nullable=False),
        sa.Column('hashed_password', sa.String(), nullable=False),
        sa.Column('full_name', sa.String(), nullable=False),
        sa.Column('role', sa.String(), nullable=False),
        sa.Column('is_active', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_user_email'), 'user', ['email'], unique=False)
    op.create_index(op.f('ix_user_organization_id'), 'user', ['organization_id'], unique=False)

    # Create sector (land area)
    op.create_table(
        'sector',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('name', sa.String(), nullable=False),
        sa.Column('area_hectares', sa.Float(), nullable=True),
        sa.Column('location_notes', sa.String(), nullable=True),
        sa.Column('is_active', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_sector_name'), 'sector', ['name'], unique=False)
    op.create_index(op.f('ix_sector_organization_id'), 'sector', ['organization_id'], unique=False)

    # Create lote (plot within sector)
    op.create_table(
        'lote',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('sector_id', sa.Integer(), nullable=False),
        sa.Column('name', sa.String(), nullable=False),
        sa.Column('area_hectares', sa.Float(), nullable=True),
        sa.Column('soil_type', sa.String(), nullable=True),
        sa.Column('is_active', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['sector_id'], ['sector.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_lote_organization_id'), 'lote', ['organization_id'], unique=False)
    op.create_index(op.f('ix_lote_sector_id'), 'lote', ['sector_id'], unique=False)

    # Create task
    op.create_table(
        'task',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('title', sa.String(), nullable=False),
        sa.Column('description', sa.String(), nullable=True),
        sa.Column('priority', sa.Integer(), nullable=False),
        sa.Column('status', sa.String(), nullable=False),
        sa.Column('assigned_to_user_id', sa.Integer(), nullable=True),
        sa.Column('due_date', sa.String(), nullable=True),
        sa.Column('sector_id', sa.Integer(), nullable=True),
        sa.Column('lote_id', sa.Integer(), nullable=True),
        sa.Column('is_archived', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['assigned_to_user_id'], ['user.id']),
        sa.ForeignKeyConstraint(['sector_id'], ['sector.id']),
        sa.ForeignKeyConstraint(['lote_id'], ['lote.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_task_organization_id'), 'task', ['organization_id'], unique=False)

    # Create livestock
    op.create_table(
        'livestock',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('identifier', sa.String(), nullable=False),
        sa.Column('animal_type', sa.String(), nullable=False),
        sa.Column('breed', sa.String(), nullable=True),
        sa.Column('birth_date', sa.String(), nullable=True),
        sa.Column('weight_kg', sa.Float(), nullable=True),
        sa.Column('is_archived', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_livestock_organization_id'), 'livestock', ['organization_id'], unique=False)

    # Create livestock_event
    op.create_table(
        'livestock_event',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('livestock_id', sa.Integer(), nullable=False),
        sa.Column('event_type', sa.String(), nullable=False),
        sa.Column('event_date', sa.String(), nullable=False),
        sa.Column('notes', sa.String(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['livestock_id'], ['livestock.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_livestock_event_organization_id'), 'livestock_event', ['organization_id'], unique=False)

    # Create crop
    op.create_table(
        'crop',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('crop_type', sa.String(), nullable=False),
        sa.Column('sector_id', sa.Integer(), nullable=True),
        sa.Column('lote_id', sa.Integer(), nullable=True),
        sa.Column('planting_date', sa.String(), nullable=True),
        sa.Column('harvest_date', sa.String(), nullable=True),
        sa.Column('quantity_planted', sa.Float(), nullable=True),
        sa.Column('quantity_harvested', sa.Float(), nullable=True),
        sa.Column('unit', sa.String(), nullable=True),
        sa.Column('is_archived', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['sector_id'], ['sector.id']),
        sa.ForeignKeyConstraint(['lote_id'], ['lote.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_crop_organization_id'), 'crop', ['organization_id'], unique=False)

    # Create crop_treatment
    op.create_table(
        'crop_treatment',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('crop_id', sa.Integer(), nullable=False),
        sa.Column('treatment_type', sa.String(), nullable=False),
        sa.Column('treatment_date', sa.String(), nullable=False),
        sa.Column('product_name', sa.String(), nullable=True),
        sa.Column('quantity', sa.Float(), nullable=True),
        sa.Column('notes', sa.String(), nullable=True),
        sa.Column('is_archived', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['crop_id'], ['crop.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_crop_treatment_organization_id'), 'crop_treatment', ['organization_id'], unique=False)

    # Create accounting_transaction
    op.create_table(
        'accounting_transaction',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('transaction_type', sa.String(), nullable=False),
        sa.Column('amount', sa.Float(), nullable=False),
        sa.Column('category', sa.String(), nullable=False),
        sa.Column('transaction_date', sa.String(), nullable=False),
        sa.Column('description', sa.String(), nullable=True),
        sa.Column('related_task_id', sa.Integer(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['related_task_id'], ['task.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_accounting_transaction_organization_id'), 'accounting_transaction', ['organization_id'], unique=False)

    # Create notification
    op.create_table(
        'notification',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('user_id', sa.Integer(), nullable=False),
        sa.Column('title', sa.String(), nullable=False),
        sa.Column('message', sa.String(), nullable=False),
        sa.Column('notification_type', sa.String(), nullable=False),
        sa.Column('is_read', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['user_id'], ['user.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_notification_organization_id'), 'notification', ['organization_id'], unique=False)

    # Create weather_observation
    op.create_table(
        'weather_observation',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('sector_id', sa.Integer(), nullable=True),
        sa.Column('observation_date', sa.String(), nullable=False),
        sa.Column('weather_type', sa.String(), nullable=False),
        sa.Column('notes', sa.String(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['sector_id'], ['sector.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_weather_observation_organization_id'), 'weather_observation', ['organization_id'], unique=False)

    # Create asset
    op.create_table(
        'asset',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('name', sa.String(), nullable=False),
        sa.Column('asset_type', sa.String(), nullable=False),
        sa.Column('purchase_date', sa.String(), nullable=True),
        sa.Column('status', sa.String(), nullable=False),
        sa.Column('is_archived', sa.Boolean(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_asset_organization_id'), 'asset', ['organization_id'], unique=False)

    # Create maintenance_log
    op.create_table(
        'maintenance_log',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('organization_id', sa.Integer(), nullable=False),
        sa.Column('asset_id', sa.Integer(), nullable=False),
        sa.Column('maintenance_date', sa.String(), nullable=False),
        sa.Column('maintenance_type', sa.String(), nullable=False),
        sa.Column('notes', sa.String(), nullable=True),
        sa.Column('cost', sa.Float(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=False),
        sa.ForeignKeyConstraint(['organization_id'], ['organization.id']),
        sa.ForeignKeyConstraint(['asset_id'], ['asset.id']),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_maintenance_log_organization_id'), 'maintenance_log', ['organization_id'], unique=False)


def downgrade() -> None:
    op.drop_table('maintenance_log')
    op.drop_table('asset')
    op.drop_table('weather_observation')
    op.drop_table('notification')
    op.drop_table('accounting_transaction')
    op.drop_table('crop_treatment')
    op.drop_table('crop')
    op.drop_table('livestock_event')
    op.drop_table('livestock')
    op.drop_table('task')
    op.drop_table('lote')
    op.drop_table('sector')
    op.drop_table('user')
    op.drop_table('organization')
