import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format date to YYYY-MM-DD format required by HTML date input elements
 */
export function formatDateForInput(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    
    // Get year, month, and day as individual components
    const year = date.getFullYear();
    // Add 1 to month since getMonth() returns 0-based index (0 = January)
    // And pad with leading zero if needed
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    
    // Return in yyyy-mm-dd format
    return `${year}-${month}-${day}`;
}
