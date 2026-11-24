import { render, screen } from '@testing-library/react'
import { expect, test } from 'vitest'
import App from './App'

test('renders the app', () => {
  render(<App />)
  expect(screen.getByText(/get started/i)).toBeInTheDocument()
})